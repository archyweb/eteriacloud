<?php
//error_reporting(E_ALL); ini_set('display_errors', TRUE); ini_set('display_startup_errors', TRUE);

class ACube
{
    public $credentials;

    public function __construct() {

        global $app;

        $credentials = $app->options['acube'];
        $mode = $app->config['acube_mode'];
        $this->credentials = $credentials[$mode];

        $params = func_get_args();
        $params_count = func_num_args();
        if (method_exists($this, $f = '__construct' . $params_count)):
            call_user_func_array(array($this, $f), $params);
        endif;
    }

    public function __construct1($mode) {
        $this->url = $mode == 'production' ? $this->api_url : $this->api_sandbox_url;
    }

    public function call($url, $method, $header, $data = NULL) {
        $options = [
            'http' => [
                'header' => $header,
                'method' => $method,
                'content' => $data,
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($options);
        $api_response = file_get_contents($this->credentials['url'] . $url, false, $context);

        return json_decode($api_response);
    }

    public function login() {
        $credentials = ['email' => $this->credentials['email'], 'password' => $this->credentials['password']];
        $header = ["Cache-Control: no-cache", "Content-Type: application/json"];
        $result = $this->call('/login_check', 'POST', $header, json_encode($credentials));
        $token = $result->token;

        return $token;
    }

    public function send_invoice($sdi_xml_path) {
        $invoice = file_get_contents($sdi_xml_path);
        $token = $this->login();
        $header = ["Cache-Control: no-cache", "Content-Type: application/xml", "Authorization: Bearer " . $token];

        return $this->call('/invoices', 'POST', $header, $invoice);
    }

    public function send_invoice_json($sdi_json_path) {
        $invoice = file_get_contents($sdi_json_path);
        $token = $this->login();
        $header = ["Cache-Control: no-cache", "Accept: application/json", "Content-Type: application/json", "Authorization: Bearer " . $token];

        return $this->call('/invoices', 'POST', $header, $invoice);
    }

    public function get_xml($uuid) {
        $token = $this->login();
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->credentials['url'] . "/invoices/" . $uuid,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Accept: application/xml",
                "Authorization: Bearer " . $token,
                "Cache-Control: no-cache",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        return $response;

        /*if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }*/
    }

    public function invoices_collection($vat_id, $before = NULL, $after = NULL) {
        $token = $this->login();
        $header = ["Cache-Control: no-cache", "Content-Type: application/json", "Authorization: Bearer " . $token];

        $url = "/invoices?type=1&recipient=" . $vat_id;
        if ($before):
            $url .= "&createdAt[before]=" . $before;
        endif;
        if ($after):
            $url .= "&createdAt[after]=" . $after;
        endif;
        //error_log("@@@@@@@@@@@@@@@@@@@ invoices_collection url: ".$url);

        return $this->call($url, 'GET', $header);
    }

    public function simulate_customer_notification($notification_type, $invoice_id) {
        $invoice = new Invoice($invoice_id);

        $xml = file_get_contents($invoice->sdi_xml_path);
        echo $invoice->sdi_xml_path . '<br>' . $invoice->sdi_uuid . '<br>';

        $token = $this->login();
        $header = ["Cache-Control: no-cache", "Content-Type: application/json", "Authorization: Bearer " . $token];
        $url = '/simulate/customer-notification/' . $notification_type . '/' . $invoice->sdi_uuid;

        $response = $this->call($url, 'POST', $header);

        $this->dump($response);
    }

    public function simulate_supplier_invoice() { //accepts invoices in json format only
        $token = $this->login();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-sandbox.acubeapi.com/simulate/supplier-invoice",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"fattura_elettronica_header\": {\n    \"dati_trasmissione\": {\n      \"codice_destinatario\": \"1234567\"\n    },\n    \"cedente_prestatore\": {\n      \"dati_anagrafici\": {\n        \"id_fiscale_iva\": {\n          \"id_paese\": \"IT\",\n          \"id_codice\": \"12345678901\"\n        },\n        \"anagrafica\": {\n          \"denominazione\": \"John Doe\"\n        },\n        \"regime_fiscale\": \"RF01\"\n      },\n      \"sede\": {\n        \"indirizzo\": \"Via di Qua, 1\",\n        \"cap\": \"20145\",\n        \"comune\": \"Milano\",\n        \"provincia\": \"MI\",\n        \"nazione\": \"IT\"\n      }\n    },\n    \"cessionario_committente\": {\n      \"dati_anagrafici\": {\n        \"id_fiscale_iva\": {\n          \"id_paese\": \"IT\",\n          \"id_codice\": \"12345678901\"\n        },\n        \"anagrafica\": {\n          \"denominazione\": \"Jane Doe\"\n        }\n      },\n      \"sede\": {\n        \"indirizzo\": \"Via di La, 2\",\n        \"cap\": \"20145\",\n        \"comune\": \"Milano\",\n        \"provincia\": \"MI\",\n        \"nazione\": \"IT\"\n      }\n    }\n  },\n  \"fattura_elettronica_body\": [{\n    \"dati_generali\": {\n      \"dati_generali_documento\": {\n        \"tipo_documento\": \"TD04\",\n        \"divisa\": \"EUR\",\n        \"data\": \"2018-07-10\",\n        \"numero\": \"1\"\n      }\n    },\n    \"dati_beni_servizi\": {\n      \"dettaglio_linee\": [{\n        \"numero_linea\": 1,\n        \"descrizione\": \"Descrizione articolo\",\n        \"prezzo_unitario\": \"10.00\",\n        \"prezzo_totale\": \"10.00\",\n        \"aliquota_iva\": \"22.00\"\n      }],\n      \"dati_riepilogo\": [{\n        \"aliquota_iva\": \"22.00\",\n        \"imponibile_importo\": \"10.00\",\n        \"imposta\": \"2.20\"\n      }]\n    }\n  }]\n}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $token,
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err):
            echo "cURL Error #:" . $err;
        else:
            echo $response;
        endif;
    }

    public function dump($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
}
