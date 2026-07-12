<?php

class Invoice
{

    public
        $app, $db, $year;

    public
        //invoice
        $id,
        $firm_id,
        $number,
        $invoice_number, /* with prefix and number length */
        $date,
        $customer_id,
        $invoice,
        $payment_method_id,
        $payment_method, /* joined */
        $payment_method_code, /* joined */
        $due_date,
        $taxable_amount,
        $vat,
        $total,
        $payment_status,
        $notes,
        $sent_on,
        $file_path,
        $created_at,
        $updated_at;

    public
        $firm; /* instance of Firm class */

    public
        $customer; /* instance of Registry class */

    public
        $folder, $file_name, $file_physical_path, $main_folder, $sdi_xml_path, $sdi_json_path;

    public
        $shortcodes, $replaces;

    public function __construct() {
        global $app;
        $this->db = $app->get_db();
        $this->app =& $app;

        $this->shortcodes = [
            '[NumeroFattura]',
            '[DataFattura]',
            '[ImportoFattura]',
            '[ScadenzaFattura]',
            '[CorpoFattura]',
            '[NoteFattura]'
        ];

        $this->main_folder = 'assets/documents/invoices';

        $params = func_get_args();
        $params_count = func_num_args();
        if (method_exists($this, $f = '__construct' . $params_count)):
            call_user_func_array(array($this, $f), $params);
        endif;
    }

    public function __construct1($id) {

        $columns = mysqli_fetch_object($this->db->query("
			select 
				archy_invoices.*, 
				archy_payment_methods.method as payment_method,
                archy_payment_methods.payment_code as payment_method_code
			from archy_invoices 
			left join archy_payment_methods 
			on (archy_invoices.payment_method_id=archy_payment_methods.id)
			where archy_invoices.id=" . $id)
        );

        foreach ($columns as $name => $value):
            $this->$name = $value;
        endforeach;
        $this->invoice = unserialize($this->invoice);
        $this->firm = new Firm($this->firm_id);
        $this->customer = new Registry($this->customer_id);
        $this->latin_date = App::latin_date($this->date);

        $this->year = (new DateTime($this->date))->format('Y');
        $this->invoice_number = $this->firm->invoice_prefix . str_repeat("0", $this->firm->invoice_number_length - strlen($this->number)) . $this->number . '-' . $this->year;

        $this->folder = $this->app->subdomain_root . '/' . $this->main_folder . '/' . $this->year . '/' . $this->firm_id;
        $this->file_name = $this->invoice_number . '_' . App::file_system_string($this->customer->label) . '.pdf';
        $this->xml_file_name = $this->id . '.xml';
        $this->json_file_name = $this->id . '.json';
        $this->file_physical_path = $this->folder . '/' . $this->file_name;

        $this->sdi_xml_path = $this->folder . '/' . $this->xml_file_name;
        $this->sdi_json_path = $this->folder . '/' . $this->json_file_name;

        $this->replaces = [
            $this->invoice_number,
            $this->latin_date,
            App::currency($this->total),
            App::latin_date($this->due_date),
            $this->app->invoice_table($this->invoice, $this->taxable_amount, $this->vat, $this->total),
            $this->notes
        ];

    }

    public function add($invoice_row){
		$q="INSERT INTO archy_invoices (".join(',',array_keys($invoice_row)).") values (".join(',',array_values($invoice_row)).")";
        $ret=$this->db->query($q);
        show_query_error($ret,$this->db,$q);
        $new_invoice=new Invoice($this->db->insert_id);
		$new_invoice->store();
		return $new_invoice;
	}

    public function store() {

        require_once $this->app->main_root . '/private/_common/classes/mpdf/vendor/autoload.php';

        $pdf = $this->build_pdf();

        if (!is_dir($this->folder)):
            mkdir($this->folder, 0775, true);
        endif;

        if (is_file($this->file_physical_path)):
            unlink($this->file_physical_path);
        endif;

        $footer = $this->firm->invoice_footer;
        $footer = str_replace($this->customer->shortcodes, $this->customer->replaces, $footer);
        $footer = str_replace($this->shortcodes, $this->replaces, $footer);

        $mpdf = new \mPDF('utf-8', 'A4-P');
        $mpdf->SetHTMLFooter($footer);
        $mpdf->WriteHTML($pdf);
        $mpdf->Output($this->file_physical_path, 'F');

        $file_path = '/' . $this->main_folder . '/' . $this->year . '/' . $this->firm_id . '/' . $this->file_name;
        $this->db->query("update archy_invoices set file_path='" . addslashes($file_path) . "', unpaid=total-paid where id=" . $this->id);

        $this->app->sdi_xml($this, $document_type = $this->document_type, $number = $this->invoice_number, $body_location = 'invoice', $file_path = $this->sdi_xml_path);
        $this->app->sdi_json($this, $document_type = $this->document_type, $number = $this->invoice_number, $body_location = 'invoice', $file_path = $this->sdi_json_path);
    }

    public function send_to_sdi() {

        $this->db->query("update archy_invoices set sdi_sent_on=now() where id=" . $this->id);
        $acube = new ACube();
        $response = $acube->send_invoice($this->sdi_xml_path);
        if (isset($response->uuid)):
            $this->db->query("update archy_invoices set sdi_uuid='" . $response->uuid . "' where id=" . $this->id);
            set_message("success", "La fattura " . $this->invoice_number . " è stata inviata al sistema di interscambio.");
        else:
            $this->open_sdi_ticket();
            set_message("danger", "Non è stato possibile inviare la fattura " . $this->invoice_number . " al sistema di interscambio. &Egrave; stato aperto automaticamente un ticket al riguardo, verrà contattato dall'assistenza tecnica.");
        endif;

        header('location: ' . $_SERVER['HTTP_REFERER']);

    }

    public function send_to_sdi_json() {

        $this->db->query("update archy_invoices set sdi_sent_on=now() where id=" . $this->id);
        $acube = new ACube();
        $response = $acube->send_invoice_json($this->sdi_json_path);
        if (isset($response->uuid)):
            $this->db->query("update archy_invoices set sdi_uuid='" . $response->uuid . "' where id=" . $this->id);
            set_message("success", "La fattura " . $this->invoice_number . " è stata inviata al sistema di interscambio.");
        else:
            error_log("SDI INVOICE ERROR: " . json_encode($response));
            $this->open_sdi_ticket(json_encode($response));
            set_message("danger", "Non è stato possibile inviare la fattura " . $this->invoice_number . " al sistema di interscambio. &Egrave; stato aperto automaticamente un ticket al riguardo, verrà contattato dall'assistenza tecnica.");
        endif;

        header('location: ' . $_SERVER['HTTP_REFERER']);

    }

    public function download_sdi_xml() {

        $acube = new ACube();
        $xml = $acube->get_xml($this->sdi_uuid);
        header('Content-Type: application/xml');
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate');
        header('Content-Disposition: attachment;filename="' . $this->invoice_number . '.xml"');
        echo $xml;

    }

    public function open_sdi_ticket($msg) {

        $settings = $this->app->get_settings($domain = 'notifications');

        $_POST['assistance_type'] = 'tech';
        $_POST['assistance_reply'] = $settings->sender_email;
        $_POST['assistance_subject'] = 'Errore invio fattura al Sistema Di Interscambio';
        $_POST['assistance_request'] = '<p>Errore invio Fattura ' . $this->invoice_number . "</p>" 
            . 'JSON fattura: <a href="'.substr($this->sdi_json_path,strpos($this->sdi_json_path,"/assets/")).'" target="_blank"> Download </a> <br>'
            . "<code>Error: " . $msg . "</code>"
            ;

        $support = new Support();
        $support->open_ticket();

    }

    public function sdi_error_notification_ticket($errors, $subdomain) {
        $settings = $this->app->get_settings($domain = 'notifications');
        $_POST['domain'] = $subdomain;
        $_POST['assistance_type'] = 'tech';
        $_POST['assistance_reply'] = $settings->sender_email;
        $_POST['assistance_subject'] = 'Notifica di scarto fattura ' . $this->invoice_number . ' dal Sistema Di Interscambio';
        $_POST['assistance_request'] = '<p>La fattura ' . $this->invoice_number . " è stata rigettata dal sistema di interscambio</p><p><code>Path fattura:<br>" . $this->sdi_xml_path . "</code></p><p><code>Errore: " . json_encode($errors) . "</code></p>";

        $support = new Support();
        $support->open_ticket($avoid_redirect = true);

    }

    public function after_update() {

        $this->app->invoice_balance($this->id);

    }

    public function after_delete() {

        $this->db->query("delete from archy_credit_notes where invoice_id=" . $this->id);
        $this->db->query("delete from archy_petty_cash_book where document_table='archy_invoices' and document_id=" . $this->id);

    }

    public function build_pdf() {

        $template = $this->firm->invoice_template; // firm's shortcodes have been replaced in Firm class
        $template = str_replace($this->customer->shortcodes, $this->customer->replaces, $template);
        $template = str_replace($this->shortcodes, $this->replaces, $template);

        $styles = '<style type="text/css">';
        $styles .= file_get_contents($this->app->get_subdomain_url() . '/assets/tinymce/templates/content_css.css');
        $styles .= '</style>';

        return '<html><head>' . $styles . '</head><body>' . $template . '</body></html>';

    }

    public function send() {

        if ($this->customer->email != ''):

            $message = new Message();

            $mail_subject = $this->firm->invoice_mail_subject;
            $mail_subject = str_replace($this->shortcodes, $this->replaces, $mail_subject);
            $mail_subject = str_replace($this->firm->shortcodes, $this->firm->replaces, $mail_subject);
            $mail_subject = str_replace($this->customer->shortcodes, $this->customer->replaces, $mail_subject);

            $mail_body = $this->firm->invoice_mail_body;
            $mail_body = str_replace($this->shortcodes, $this->replaces, $mail_body);
            $mail_body = str_replace($this->firm->shortcodes, $this->firm->replaces, $mail_body);
            $mail_body = str_replace($this->customer->shortcodes, $this->customer->replaces, $mail_body);

            $email = [
                'recipient_name' => $this->customer->firm_name,
                'recipient_mail' => $this->customer->email,
                'sender_name' => $this->firm->firm_name,
                'sender_mail' => $this->firm->email,
                'subject' => $mail_subject,
                'message' => $mail_body,
                'attachments' => [$this->file_name => file_get_contents($this->file_physical_path)]
            ];
            if ($this->firm->invoice_mail_bcc != ''):
                $bcc = explode(',', $this->firm->invoice_mail_bcc);
                $email['bcc'] = array_map('trim', $bcc);
            endif;

            $message->email($email);

            $this->db->query("update archy_invoices set sent_on=NOW() where id=" . $this->id);

            set_message("success", "Fattura inviata correttamente.");

        else:

            set_message("danger", "Non è stato possibile inviare la fattura, al cliente non è associato un indirizzo e-mail.");

        endif;

        header('location: ' . $_SERVER['HTTP_REFERER']);

    }

    public function duplicate() {

        $_POST['year'] = date('Y');
        $_POST['firm_id'] = $this->firm_id;
        $_POST['table'] = 'archy_invoices';
        $_POST['first_number'] = 'first_invoice_number';
        $number = $this->app->next_document_number_by_year_and_firm('archy_invoices', $this->firm_id, date('Y'), 'first_invoice_number', $ajax = false);

        $this->db->query("
			insert into archy_invoices set
			document_type='" . $this->document_type . "',
			VAT_collectability='" . $this->VAT_collectability . "',
			firm_id=" . $this->firm_id . ",
			number=" . $number . ",
			date='" . date('Y-m-d') . "',
			customer_id=" . $this->customer_id . ",
			invoice='" . addslashes(serialize($this->invoice)) . "',
			payment_method_id=" . (empty($this->payment_method_id) ? 'NULL' : $this->payment_method_id) . ",
			taxable_amount=" . $this->taxable_amount . ",
			vat=" . $this->vat . ",
			total=" . $this->total . ",
			payment_status='not_paid',
			notes='" . addslashes($this->notes) . "'
		");

        $new_id = $this->db->insert_id;

        $invoice = new Invoice($new_id);
        $invoice->store();

        header('location:/archy/app.php?intent=archy_invoices&view=edit&id=' . $new_id);

    }

    public function stats() {

        $invoices = $this->db->query("select * from archy_invoices");
        $stats = $monthly_stats = [];
        while ($invoice = mysqli_fetch_object($invoices)):
            list($year, $month, $date) = explode('-', $invoice->date);
            @$stats[$year] += $invoice->total;
            @$monthly_stats[$year][$month] += $invoice->total;
        endwhile;

        $return = new stdClass();
        $return->revenue_by_year = [['Anno', 'Fatturato']];
        $return->monthly_average_by_year = [['Anno', 'Media mensile']];
        $head = ['Anno'];

        foreach ($stats as $year => $amount):
            $return->revenue_by_year[] = [$year . '', $amount];
            $return->monthly_average_by_year[] = [$year . '', $amount / 12];
            $head[] = $year . '';
        endforeach;

        $return->revenue_by_month_and_year = [$head];

        $month_names = $this->app->months; // ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];
        $month_index = 0;

        for ($i = 1; $i <= 12; $i++):
            $tmp = [$month_names[$month_index]];
            $j = $i < 10 ? '0' . $i : $i . '';
            foreach ($monthly_stats as $year => $months):
                $tmp[] = isset($months[$j]) ? $months[$j] : 0;
            endforeach;
            $return->revenue_by_month_and_year[] = $tmp;
            $month_index++;
        endfor;

        return $return;

    }

}
