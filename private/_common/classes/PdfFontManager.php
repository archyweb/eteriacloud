<?php
/**
 * PdfFontManager
 *
 * Gestione controllata dei font utilizzabili nei modelli PDF.
 *
 * Versione 2.0 prudente:
 * - mantiene i font base TCPDF sempre disponibili;
 * - legge font TTF comuni da /private/_common/assets/fonts/pdf/;
 * - legge font TTF cliente da /assets/fonts/pdf/ del sottodominio;
 * - supporta anche una cartella privata cliente, se presente;
 * - genera la lista font per TinyMCE;
 * - registra i TTF in TCPDF prima di writeHTML();
 * - normalizza i font-family salvati da TinyMCE verso i nomi interni TCPDF.
 *
 * Nota:
 * I font Google NON vanno richiamati online durante la generazione PDF.
 * Vanno scaricati in .ttf e caricati in una delle cartelle supportate.
 */
class PdfFontManager {

    private $app;
    private $fontMap = [];
    private $displayFonts = [];

    public function __construct($app){
        $this->app = $app;
        $this->fontMap = $this->getBaseFontMap();
        $this->displayFonts = $this->getBaseDisplayFonts();
    }

    /**
     * Font nativi TCPDF o già gestiti come fallback sicuro.
     */
    private function getBaseFontMap(){
        return [
            'helvetica' => 'helvetica',
            'Helvetica' => 'helvetica',
            'arial' => 'helvetica',
            'Arial' => 'helvetica',

            'times' => 'times',
            'Times' => 'times',
            'times new roman' => 'times',
            'Times New Roman' => 'times',

            'courier' => 'courier',
            'Courier' => 'courier',
            'courier new' => 'courier',
            'Courier New' => 'courier',

            // Font già verificati nel sistema attuale.
            'roboto' => 'roboto',
            'Roboto' => 'roboto',
            'montserrat' => 'montserrat',
            'Montserrat' => 'montserrat',
            'lora' => 'lora',
            'Lora' => 'lora',
            'open sans' => 'opensans',
            'Open Sans' => 'opensans',
        ];
    }

    private function getBaseDisplayFonts(){
        return [
            'Helvetica' => 'helvetica',
            'Arial' => 'arial,helvetica,sans-serif',
            'Times New Roman' => 'times new roman,times',
            'Courier New' => 'courier new,courier',
            'Roboto' => 'roboto',
            'Montserrat' => 'montserrat',
            'Lora' => 'lora',
            'Open Sans' => 'open sans',
        ];
    }

    /**
     * Cartelle font supportate.
     *
     * Common:
     * /private/_common/assets/fonts/pdf/montserrat/Montserrat-Regular.ttf
     *
     * Cliente pubblico tecnico:
     * /subdomains/demo/httpdocs/assets/fonts/pdf/font-cliente/FontCliente-Regular.ttf
     *
     * Cliente privato opzionale:
     * /subdomains/demo/private/fonts/pdf/font-cliente/FontCliente-Regular.ttf
     */
    private function getFontDirectories(){
        $dirs = [];

        if(isset($this->app->main_root)):
            $dirs[] = rtrim($this->app->main_root, '/').'/private/_common/assets/fonts/pdf';
        endif;

        if(isset($this->app->subdomain_root)):
            $subdomainRoot = rtrim($this->app->subdomain_root, '/');
            $dirs[] = $subdomainRoot.'/assets/fonts/pdf';

            // Esempio: /subdomains/demo/httpdocs -> /subdomains/demo/private/fonts/pdf
            $customerRoot = dirname($subdomainRoot);
            $dirs[] = rtrim($customerRoot, '/').'/private/fonts/pdf';
        endif;

        return array_values(array_unique($dirs));
    }

    private function humanizeFontName($value){
        $value = (string)$value;
        $value = preg_replace('/\.(ttf|otf)$/i', '', $value);
        $value = preg_replace('/\b(regular|bold|italic|medium|light|semibold|semi-bold|black|thin|extra-bold|extrabold)\b/i', '', $value);
        $value = str_replace(['-', '_'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);
        return trim(ucwords(strtolower($value)));
    }

    private function normalizeKey($value){
        $value = trim((string)$value);
        $value = trim($value, "'\"");
        $value = strtolower($value);
        $value = preg_replace('/\s+/', ' ', $value);
        return $value;
    }

    private function cssFamilyValue($displayName){
        $displayName = trim((string)$displayName);
        if(strpos($displayName, ' ') !== false):
            return "'".$displayName."'";
        endif;
        return $displayName;
    }

    private function findTtfFiles($dir){
        if(!is_dir($dir)):
            return [];
        endif;

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );

        foreach($iterator as $file):
            if(!$file->isFile()):
                continue;
            endif;

            $ext = strtolower($file->getExtension());

			if(in_array($ext, ['ttf', 'otf'])):
				$files[] = $file->getPathname();
			endif;
        endforeach;

        return $files;
    }

    private function detectDisplayNameFromPath($filePath, $baseDir){
        $relative = str_replace(rtrim($baseDir, '/').'/', '', $filePath);
        $parts = explode('/', $relative);

        // Se il font è in una sottocartella, il nome famiglia viene preso dalla cartella.
        if(count($parts) > 1):
            return $this->humanizeFontName($parts[0]);
        endif;

        return $this->humanizeFontName(pathinfo($filePath, PATHINFO_FILENAME));
    }

    /**
     * Scansiona le cartelle e aggiorna la lista font disponibile per TinyMCE.
     */
    public function scanAvailableFonts(){
        foreach($this->getFontDirectories() as $dir):
            foreach($this->findTtfFiles($dir) as $ttfFile):
                $displayName = $this->detectDisplayNameFromPath($ttfFile, $dir);
                if($displayName == ''):
                    continue;
                endif;

                if(!isset($this->displayFonts[$displayName])):
                    $this->displayFonts[$displayName] = $this->cssFamilyValue($displayName);
                endif;
            endforeach;
        endforeach;

        ksort($this->displayFonts, SORT_NATURAL | SORT_FLAG_CASE);
        return $this->displayFonts;
    }

    /**
     * Restituisce la stringa font_formats per TinyMCE.
     */
    public function getTinyMceFontFormats(){
        $fonts = $this->scanAvailableFonts();
        $items = [];

        foreach($fonts as $label => $cssFamily):
            $items[] = $label.'='.$cssFamily;
        endforeach;

        return implode('; ', $items);
    }

    /**
     * Registra in TCPDF tutti i TTF trovati.
     */
   public function registerFonts($tcpdf){
    if(!class_exists('TCPDF_FONTS')):
        return $this->fontMap;
    endif;

    foreach($this->getFontDirectories() as $dir):
        foreach($this->findTtfFiles($dir) as $ttfFile):

            $displayName = $this->detectDisplayNameFromPath($ttfFile, $dir);

            if($displayName == ''):
                continue;
            endif;

            $filename = strtolower(pathinfo($ttfFile, PATHINFO_FILENAME));

            $tcpdfFontName = @TCPDF_FONTS::addTTFfont(
                $ttfFile,
                'TrueTypeUnicode',
                '',
                96
            );

            if(!$tcpdfFontName):
                continue;
            endif;

            $familyKey = $this->normalizeKey($displayName);

            if(strpos($filename, 'bolditalic') !== false || strpos($filename, 'bold-italic') !== false || strpos($filename, 'bold italic') !== false):
                $variant = 'bolditalic';
            elseif(strpos($filename, 'bold') !== false):
                $variant = 'bold';
            elseif(strpos($filename, 'italic') !== false):
                $variant = 'italic';
            else:
                $variant = 'regular';
            endif;

            $this->fontMap[$familyKey] = $this->fontMap[$familyKey] ?? [];
            $this->fontMap[$familyKey][$variant] = $tcpdfFontName;

            $this->fontMap[$displayName] = $this->fontMap[$familyKey];

        endforeach;
    endforeach;

    return $this->fontMap;
}

    /**
     * Normalizza i font-family salvati da TinyMCE in nomi capiti da TCPDF.
     */
    public function normalizeHtmlFontFamilies($html){
    if(trim((string)$html) == ''):
        return $html;
    endif;

    $map = $this->fontMap;
    $self = $this;

    return preg_replace_callback(
        '/<span([^>]*)style="([^"]*font-family\s*:\s*[^;]+;?[^"]*)"([^>]*)>(.*?)<\/span>/is',
        function($matches) use ($map, $self){

            $before = $matches[1];
            $style = $matches[2];
            $after = $matches[3];
            $content = $matches[4];

            preg_match('/font-family\s*:\s*([^;]+);?/i', $style, $fontMatch);

            if(empty($fontMatch[1])):
                return $matches[0];
            endif;

            $families = explode(',', $fontMatch[1]);
            $familyKey = null;

            foreach($families as $family):
                $clean = trim($family);
                $clean = trim($clean, "'\"");
                $key = $self->normalizeKey($clean);

                if(isset($map[$key]) && is_array($map[$key])):
                    $familyKey = $key;
                    break;
                endif;
            endforeach;

            if($familyKey === null):
                return $matches[0];
            endif;

            $isBold = preg_match('/<(strong|b)[^>]*>/i', $content);
            $isItalic = preg_match('/<(em|i)[^>]*>/i', $content);

            if($isBold && $isItalic && isset($map[$familyKey]['bolditalic'])):
                $font = $map[$familyKey]['bolditalic'];
            elseif($isBold && isset($map[$familyKey]['bold'])):
                $font = $map[$familyKey]['bold'];
            elseif($isItalic && isset($map[$familyKey]['italic'])):
                $font = $map[$familyKey]['italic'];
            elseif(isset($map[$familyKey]['regular'])):
                $font = $map[$familyKey]['regular'];
            else:
                $font = 'helvetica';
            endif;

            $style = preg_replace(
                '/font-family\s*:\s*[^;]+;?/i',
                'font-family: '.$font.';',
                $style
            );

            return '<span'.$before.'style="'.$style.'"'.$after.'>'.$content.'</span>';
        },
        $html
    );
}
}
