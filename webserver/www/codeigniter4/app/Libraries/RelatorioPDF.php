<?php

require_once(APPPATH . 'Libraries/fpdf/fpdf.php');

class RelatorioPDF extends FPDF
{
    function Header()
    {
        $logoWidth = 50; // Largura da logo
        $pageWidth = $this->GetPageWidth(); // Largura total da página
        $x = ($pageWidth - $logoWidth) / 2; // Calcula posição X centralizada

        $this->Image(FCPATH . 'assets/images/sd_logo.png', $x, 6, $logoWidth); // Centralizado
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}
