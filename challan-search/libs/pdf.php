<?php
require_once plugin_dir_path(__FILE__) . '../fpdf/fpdf.php';

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Ln(5); // Line break for spacing
    }

    // Footer method to define the bottom of each page
    function Footer() {
        $this->SetY(-15); // Position the footer 1.5 cm from the bottom
        $this->SetFont('Arial', 'I', 8);
        // $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C'); // Page numbering
    }
}
?>
