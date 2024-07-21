<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use TCPDF;

class TransaksiController extends BaseController
{
    protected $cart;
    protected $url = "https://api.rajaongkir.com/starter/";
    protected $apiKey = "18242884a5cd12d0975793d19e9fc833";
    protected $transaction;
    protected $transaction_detail;

    function __construct()
    {
        helper('number');
        helper('form');
        $this->cart = \Config\Services::cart();
        $this -> transaction = new TransactionModel();
        $this -> transaction_detail = new TransactionDetailModel();
    }

    public function index()
    {
        $data['items'] = $this->cart->contents();
        $data['total'] = $this->cart->total();
        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
        $this->cart->insert(array(
            'id'        => $this->request->getPost('id'),
            'qty'       => 1,
            'price'     => $this->request->getPost('harga'),
            'name'      => $this->request->getPost('nama'),
            'options'   => array('foto' => $this->request->getPost('foto'))
        ));
        session()->setflashdata('success', 'Produk berhasil ditambahkan ke keranjang. (<a href="' . base_url() . 'keranjang">Lihat</a>)');
        return redirect()->to(base_url('/'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();
        session()->setflashdata('success', 'Keranjang Berhasil Dikosongkan');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $value) {
            $this->cart->update(array(
                'rowid' => $value['rowid'],
                'qty'   => $this->request->getPost('qty' . $i++)
            ));
        }

        session()->setflashdata('success', 'Keranjang Berhasil Diedit');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);
        session()->setflashdata('success', 'Keranjang Berhasil Dihapus');
        return redirect()->to(base_url('keranjang'));
    }

    public function checkout()
    {
        $data['items'] = $this->cart->contents();
        $data['total'] = $this->cart->total();
        $provinsi = $this->rajaongkir('province');
				$data['provinsi'] = json_decode($provinsi)->rajaongkir->results;

        return view('v_checkout', $data);
    }

    public function getCity()
    {
        if ($this->request->isAJAX()) {
            $id_province = $this->request->getGet('id_province');
            $data = $this->rajaongkir('city', $id_province);
            return $this->response->setJSON($data);
        }
    }

    public function getCost()
    {
        if ($this->request->isAJAX()) {
            $origin = $this->request->getGet('origin');
            $destination = $this->request->getGet('destination');
            $weight = $this->request->getGet('weight');
            $courier = $this->request->getGet('courier');
            $data = $this->rajaongkircost($origin, $destination, $weight, $courier);
            return $this->response->setJSON($data);
        }
    }

    private function rajaongkircost($origin, $destination, $weight, $courier)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=" . $origin . "&destination=" . $destination . "&weight=" . $weight . "&courier=" . $courier,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: " . $this->apiKey,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return $response;
    }


    private function rajaongkir($method, $id_province = null)
    {
        $endPoint = $this->url . $method;

        if ($id_province != null) {
            $endPoint = $endPoint . "?province=" . $id_province;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: " . $this->apiKey
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return $response;
    }

    public function buy()
    {
        if ($this->request->getPost()) { 
            $dataForm = [
                'username' => $this->request->getPost('username'),
                'total_harga' => $this->request->getPost('total_harga'),
                'alamat' => $this->request->getPost('alamat'),
                'ongkir' => $this->request->getPost('ongkir'),
                'status' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $this->transaction->insert($dataForm);

            $last_insert_id = $this->transaction->getInsertID();

            foreach ($this->cart->contents() as $value) {
                $dataFormDetail = [
                    'transaction_id' => $last_insert_id,
                    'product_id' => $value['id'],
                    'jumlah' => $value['qty'],
                    'diskon' => 0,
                    'subtotal_harga' => $value['qty'] * $value['price'],
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];

                $this->transaction_detail->insert($dataFormDetail);
            }

            $this->cart->destroy();
    
            return redirect()->to(base_url('profile'));
        }
    }

    public function history()
    {
        $username = session()->get('username'); // Ambil username dari session
        $data['username'] = $username;
        $data['buy'] = $this->transaction
            ->where('username', $username) // Filter berdasarkan username
            ->findAll();
        
        $data['product'] = [];
        foreach ($data['buy'] as $item) {
            $data['product'][$item['id']] = $this->transaction_detail->where('transaction_id', $item['id'])->findAll();
        }

        return view('v_transaksi', $data);
    }

    public function ubah_status()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        
        // Update status transaksi
        $this->transaction->update($id, ['status' => $status]);

        // Redirect ke halaman transaksi
        return redirect()->to('/transaksi');
    }

    public function download()
    {
        $username = session()->get('username');
        $data['transactions'] = $this->transaction->where('username', $username)->findAll();
        $data['details'] = [];

        foreach ($data['transactions'] as $transaction) {
            $data['details'][$transaction['id']] = $this->transaction_detail->where('transaction_id', $transaction['id'])->findAll();
        }

        // Generate PDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 18);

        // Add Title
        $pdf->Cell(0, 10, 'History Transaksi Pembelian', 0, 1,'C');

        $pdf->SetFont('helvetica', '', 12);
        // Add Table Header
        $pdf->Cell(10, 10, 'No', 1);
        $pdf->Cell(30, 10, 'Username', 1);
        $pdf->Cell(40, 10, 'Total Harga', 1);
        $pdf->Cell(50, 10, 'Alamat', 1);
        $pdf->Cell(30, 10, 'Ongkir', 1);
        $pdf->Cell(20, 10, 'Status', 1);
        $pdf->Ln();

        // Add Table Rows
        foreach ($data['transactions'] as $index => $item) {
            $pdf->Cell(10, 10, $index + 1, 1);
            $pdf->Cell(30, 10, $item['username'], 1);
            $pdf->Cell(40, 10, number_to_currency($item['total_harga'], 'IDR'), 1);
            $pdf->Cell(50, 10, $item['alamat'], 1);
            $pdf->Cell(30, 10, number_to_currency($item['ongkir'], 'IDR'), 1);
            $pdf->Cell(20, 10, $item['status'], 1);
            $pdf->Ln();
        }

        // Add Date and Time
        $currentDateTime = date('Y-m-d H:i:s');
        $pdf->Cell(0, 10, 'Download: ' . $currentDateTime, 0, 1, '');
        $pdf->Ln(5); // Add a little space after the date and time

        // Generate File Name with Timestamp
        $fileName = 'transaksi_history_' . date('Ymd_His') . '.pdf';

        // Output PDF
        $pdf->Output($fileName, 'D');
        exit();
    }
    
}