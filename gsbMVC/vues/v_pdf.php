<?php
    require_once('../fpdf/fpdf.php');
    session_start();

    class PDF extends FPDF {
        function header() {
            $this->Image('../images/logo.jpg',10,6,30);
            $this->SetFont('Arial','B',15);
            $this->Cell(80);
            $this->Cell(40,10,'Fiche De Frais',1,0,'C');
            $this->Ln(20);
            $this->SetFont('Arial','',15);
            $this->Cell(65,10,$_SESSION['nomVisiteur']." ".$_SESSION['prenomVisiteur']." - Le ".$_SESSION['numMois']."/".$_SESSION['numAnnee'],1,0,'C');
            $this->Ln(20);
        }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times','',12);

    $pdf->Cell(0, 6, "Etat actuel : ".$_SESSION['libEtat']." depuis le ".$_SESSION['dateModif']);
    $pdf->Ln();
    $pdf->Cell(0, 6, "Montant valide : ".$_SESSION['montantValide']);
    $pdf->Ln(10);

    //tableau Frais
    $header = array('Forfait Etape', 'Frais Kilometrique', 'Nuitee Hotel', 'Repas Restaurant');
    $lesFraisForfait = $_SESSION['lesFraisForfait'];
    foreach($header as $col)
        $pdf->Cell(40,7,$col,1);
    $pdf->Ln();
    foreach($lesFraisForfait as $row) {
        $pdf->Cell(40, 6, $row[2], 1);
    }    
    $pdf->Ln();
    $pdf->Ln();

    //tableau HorsFrais
    $header = array('Date', 'Libelle', 'Montant');
    $lesFraisHorsForfait = $_SESSION['lesFraisHorsForfait'];
    if(isset($lesFraisHorsForfait[0][0])) {
        foreach($header as $col) {
            $pdf->Cell(40,7,$col,1);
        }
        $pdf->Ln();
    }
    foreach($lesFraisHorsForfait as $row) {
        $pdf->Cell(40,6,$row[4],1); 
        $pdf->Cell(40,6,$row[3],1);
        $pdf->Cell(40,6,$row[5],1);
        $pdf->Ln();
    }
    $pdf->Output();
?>


