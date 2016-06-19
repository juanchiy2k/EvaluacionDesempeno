<?php
require_once('conexion.php');
require_once('pdf.php');
require_once("php/PHPExcel/PHPExcel.php");
include('buscar.php');
include_once("funciones.php");
ob_end_clean();


// Incluimos el template engine
//include_once('includes/templateEngine.inc.php');


class Imprimir{
	private $db;
    private $text;
        
        
	public function __construct(){
            $this->db = new Conexion();
            include('internalizacion.php');
            $this->lang = $lang;
            include('locale/textos/text_layout.php');
            $this->text = $textos;

	}

	public function imprimirPlanificacion($codpersonal, $periodo, $enviamail, $aprobacion){
            if(!empty($codpersonal)){
                $personal = $this->db->prepare('CALL sp_ObtenerEmpleado(?)');
                $personal->bindParam(1, $codpersonal, PDO::PARAM_STR, 9);
                $personal->execute();

                $datospersonal = $personal->fetch(PDO::FETCH_ASSOC);
                $personal->closeCursor();


                $planificacion = $this->db->prepare('CALL sp_ObtenerPlanificacion(?,?)');
                $planificacion->bindParam(1, $codpersonal, PDO::PARAM_STR, 9);
                $planificacion->bindParam(2, $periodo, PDO::PARAM_STR, 4);
                $planificacion->execute();

                $i = 1;
                $buscar = new Buscar();
                do {
                    $rowset = $planificacion->fetchAll(PDO::FETCH_ASSOC);
                    if ($rowset) {
                        switch ($i){
                            case 1:
                                $array['mejora'] = $buscar->printResultSet($rowset, $i);
                                break;
                            case 2:
                                $array['objetivos'] = $buscar->printResultSet($rowset, $i);
                                break;
                            case 3:
                                $array['acciones'] = $buscar->printResultSet($rowset, $i);
                                break;
                        }

                    }
                    $i++;
                } while ($planificacion->nextRowset());

                $pdf=new PDF();
                $pdf->Open();
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetMargins(5,5,5);
                $pdf->Cell(50,10);
                $pdf->SetFont('Arial','B',15);
                $pdf->Cell(100,10,utf8_decode($this->text['perf_plan']),0,1,'C');
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(200,10,utf8_decode($this->text['period']).' '.$periodo,0,1,'C');
                $pdf->SetFont('Arial','B',8);
                foreach($array['mejora'] as $item){
                    if ($this->lang == 'en_EN'){
                        if ($item['estado'] == 'REGISTRADA')
                            $estado = 'REGISTERED';
                        elseif ($item['estado'] == 'APROBADA') {
                            $estado = 'APPROVED';
                        }
                            
                    }
                    else
                        $estado = $item['estado'];

                    $pdf->Cell(200,10,utf8_decode($this->text['status_planning']).': '.$estado,0,1,'C');
                }
                
                $pdf->Ln(20);
                
                $pdf->SetY(70);

                $pdf->SetFont('Arial','B',10);  
                $pdf->SetFillColor(255,0,0);
                $pdf->SetTextColor(255);
                $pdf->Cell(0, 6,utf8_decode($this->text['data_pers']), B, 1, 'C',true);
                $pdf->Ln(5);

                
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->SetFillColor(200,200,200);
                $pdf->SetTextColor(0);
                $pdf->Cell(50,5,utf8_decode($this->text['country'].': '),0,0,'',true);
                $pdf->Cell(50,5,utf8_decode($this->text['num_empl'].': '),0,0,'',true);
                $pdf->Cell(50,5,$this->text['position'].': ',0,1,'',true); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,$datospersonal['pais'],0,0); 
                $pdf->Cell(50,5,$datospersonal['codpersonal'],0,0);
                $pdf->Cell(50,5,utf8_decode($datospersonal['cargo']),0,1);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,$this->text['surnames'].': ',0,0,'',true);
                $pdf->Cell(50,5,$this->text['names'].': ',0,0,'',true);
                $pdf->Cell(50,5,utf8_decode($this->text['field'].': '),0,1,'',true); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,utf8_decode($datospersonal['apellidos']),0,0); 
                $pdf->Cell(50,5,utf8_decode($datospersonal['nombres']),0,0);
                $pdf->Cell(50,5,utf8_decode($datospersonal['area']),0,1);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,$this->text['office'].': ',0,1,'',true); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,utf8_decode($datospersonal['oficina']),0,1); 

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,utf8_decode($this->text['seniority_pos'].': '),0,0,'',true);
                $pdf->Cell(50,5,utf8_decode($this->text['seniority_pm'].': '),0,1,'',true); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,utf8_decode($datospersonal['anoscargo']),0,0); 
                $pdf->Cell(50,5,utf8_decode($datospersonal['anospm']),0,1);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,$this->text['hiera_sup'].': ',0,0,'',true);
                $pdf->Cell(50,5,'',0,0,'',true);
                $pdf->Cell(60,5,$this->text['position'].': ',0,1,'',true); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,utf8_decode($datospersonal['apellidossuperior']),0,0); 
                $pdf->Cell(50,5,utf8_decode($datospersonal['nombressuperior']),0,0);
                $pdf->Cell(50,5,utf8_decode($datospersonal['cargosuperior']),0,1);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,$this->text['reports_func'].': ',0,0,'',true);
                $pdf->Cell(50,5,'',0,0,'',true);
                $pdf->Cell(60,5,$this->text['position'].': ',0,1,'',true); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,utf8_decode($datospersonal['apellidosfuncional']),0,0); 
                $pdf->Cell(50,5,utf8_decode($datospersonal['nombresfuncional']),0,0);
                $pdf->Cell(50,5,utf8_decode($datospersonal['cargofuncional']),0,1);

                $pdf->Ln(5);
                    
                 //Objetivios para el Período
                
                $pdf->SetFont('Arial','B',10);	
                $pdf->SetFillColor(255,0,0);
                $pdf->SetTextColor(255);
                $pdf->Cell(0, 6,utf8_decode($this->text['target_per']), B, 1, 'C',true);
                $pdf->Ln(5);
                
                $pdf->SetFont('Arial','',10);	
                $pdf->SetWidths(array(50, 50, 30, 50, 15));
                $pdf->SetFont('Arial','B',10);
                $pdf->SetFillColor(216,228,232);
                $pdf->SetTextColor(0);

                for($i=0;$i<1;$i++){
                    $pdf->Row(array($this->text['target_proj'], $this->text['act_prin'], $this->text['chronogram'], $this->text['goal'], $this->text['weight']));
                }

                $pdf->SetFont('Arial','',8);
                $pdf->SetFillColor(240,240,240);
                $pdf->SetTextColor(0);
                
                foreach($array['objetivos'] as $item){
                    $pdf->Row(array(utf8_decode($item['objetivo']), utf8_decode($item['actividad']), $item['cronograma'], utf8_decode($item['meta']), $item['pesorelativo'].' %'));
                } 
                $pdf->Ln(5);
                //Acciones de Capacitaci�n y Desarrollo
                
                $pdf->SetFont('Arial','B',10);  
                $pdf->SetFillColor(255,0,0);
                $pdf->SetTextColor(255);
                $pdf->Cell(0, 6,utf8_decode($this->text['infor_cap']), B, 1, 'C',true);
                $pdf->Ln(5);
                
                $pdf->SetFont('Arial','',10);	
                $pdf->SetWidths(array(100, 50));
                $pdf->SetFont('Arial','B',10);
                $pdf->SetFillColor(240,240,240);
                $pdf->SetTextColor(0);

                for($i=0;$i<1;$i++){
                    $pdf->Row(array(utf8_decode($this->text['enter_pr_act'])));
                }

                $pdf->SetFont('Arial','',8);
                $pdf->SetFillColor(240,240,240);
                $pdf->SetTextColor(0);

                
                foreach($array['acciones'] as $item){
                    $pdf->Row(array(utf8_decode($item['accion'])));
                } 

                $pdf->Ln(5);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->SetFillColor(200,200,200);
                $pdf->SetTextColor(0);
                foreach($array['mejora'] as $item){
                    $pdf->Cell(150,5,$this->text['attitude'].': ',0,1,'',true);
                    $pdf->Cell(25,5,'',0,0,'');
                    $pdf->SetFont('Arial','',8);
                    $pdf->MultiCell(150,5,utf8_decode($item['mejora']),0,'J'); 
                    $pdf->Cell(25,5,'',0,0,'');
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Cell(150,5,utf8_decode($this->text['commitment'].': '),0,1,'',true); 
                    $pdf->Cell(25,5,'',0,0,'');
                    $pdf->SetFont('Arial','',8);
                    $pdf->MultiCell(150,5,utf8_decode($item['compromiso']),0,'J');
                }
                $pdf->Ln(5);


                $pdf->SetFont('Arial','B',10);  
                $pdf->SetFillColor(255,0,0);
                $pdf->SetTextColor(255);
                $pdf->Cell(0, 6,utf8_decode($this->text['incidence']), B, 1, 'C',true);
                $pdf->Ln(5);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->SetFillColor(200,200,200);
                $pdf->SetTextColor(0);
                $pdf->Cell(50,5,utf8_decode($this->text['target_period'].': '),0,0,'',true);
                $pdf->Cell(50,5,utf8_decode($this->text['leadership'].': '),0,1,'',true); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(25,5,'',0,0,'');
                $pdf->Cell(50,5,$datospersonal['incidenciaobjetivo'].' %',0,0); 
                $pdf->Cell(50,5,$datospersonal['incidencialiderazgo'].' %',0,1);
                $pdf->Ln(5);
                $pdf->Output('docs/'.$this->text['plan'].' '.$datospersonal['codpersonal'].'.pdf','F'); 
                //$pdf->Output('docs/'.sanear_string($datospersonal['nombres']).' '.sanear_string($datospersonal['apellidos']).'.pdf','F'); 

                $envia=($enviamail=="true")?true:false;
                if($envia){
                   include_once('email.php');

                    $email = new Email();
                    if(!$aprobacion)
                        $email->enviarPlanificacion($datospersonal);
                    else
                        $email->enviarAprobacion($datospersonal);

                }

            }
	}

    public function estadoPlanificacion($codpais){
        if(!empty($codpais)){
            $planificacion = $this->db->prepare('CALL sp_ObtenerObjetivos(?)');
            $planificacion->bindParam(1, $codpais, PDO::PARAM_STR, 3);
            $planificacion->execute();

            if($planificacion->rowCount()>0){
                // Se crea el objeto PHPExcel
                $objPHPExcel = new PHPExcel();
                // Se asignan las propiedades del libro
                $objPHPExcel->getProperties()->setCreator("Pro Mujer") //Autor
                             ->setLastModifiedBy("Pro Mujer") //Ultimo usuario que lo modificó
                             ->setTitle($this->text['obj_report'])
                             ->setSubject($this->text['obj_report'])
                             ->setDescription($this->text['obj_plan_report'])
                             ->setKeywords($this->text['obj_report'])
                             ->setCategory($this->text['excel_report']);

                $tituloReporte = $this->text['obj_report'];
                $titulosColumnas = array(strtoupper($this->text['name']), 'REGIONAL', strtoupper($this->text['office']), strtoupper($this->text['field']), strtoupper($this->text['position']), strtoupper($this->text['hiera_sup']), strtoupper($this->text['state']), strtoupper($this->text['objective']).' 1', strtoupper($this->text['objective']).' 2', strtoupper($this->text['objective']).' 3', strtoupper($this->text['objective']).' 4', strtoupper($this->text['objective']).' 5', strtoupper($this->text['objective']).' 6', strtoupper($this->text['objective']).' 7');

                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:N1');

                // Se agregan los titulos del reporte
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1',$tituloReporte)
                    ->setCellValue('A3',  $titulosColumnas[0])
                    ->setCellValue('B3',  $titulosColumnas[1])
                    ->setCellValue('C3',  $titulosColumnas[2])
                    ->setCellValue('D3',  $titulosColumnas[3])
                    ->setCellValue('E3',  $titulosColumnas[4])
                    ->setCellValue('F3',  $titulosColumnas[5])
                    ->setCellValue('G3',  $titulosColumnas[6])
                    ->setCellValue('H3',  $titulosColumnas[7])
                    ->setCellValue('I3',  $titulosColumnas[8])
                    ->setCellValue('J3',  $titulosColumnas[9])
                    ->setCellValue('K3',  $titulosColumnas[10])
                    ->setCellValue('L3',  $titulosColumnas[11])
                    ->setCellValue('M3',  $titulosColumnas[12])
                    ->setCellValue('N3',  $titulosColumnas[13]);
                
                $i = 4;
                while ($fila = $planificacion->fetch(PDO::FETCH_ASSOC)){
                    $objetivo = explode('^', $fila['objetivos']);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i,  $fila['nombre'])
                        ->setCellValue('B'.$i,  $fila['regional'])
                        ->setCellValue('C'.$i,  $fila['oficina'])
                        ->setCellValue('D'.$i,  $fila['area'])
                        ->setCellValue('E'.$i,  $fila['cargo'])
                        ->setCellValue('F'.$i,  $fila['superior'])
                        ->setCellValue('G'.$i,  $fila['estado'])
                        ->setCellValue('H'.$i, $objetivo[0])
                        ->setCellValue('I'.$i, $objetivo[1])
                        ->setCellValue('J'.$i, $objetivo[2])
                        ->setCellValue('K'.$i, $objetivo[3])
                        ->setCellValue('L'.$i, $objetivo[4])
                        ->setCellValue('M'.$i, $objetivo[5])
                        ->setCellValue('N'.$i, $objetivo[6]);
                    $i++;
                }

                $estiloTituloReporte = array(
                    'font' => array(
                        'name'      => 'Verdana',
                        'bold'      => true,
                        'italic'    => false,
                        'strike'    => false,
                        'size' =>16,
                        'color'     => array(
                            'rgb' => 'FFFFFF'
                            )
                        ),
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('argb' => 'FF220835')
                        ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_NONE                    
                            )
                        ), 
                    'alignment' =>  array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation'   => 0,
                        'wrap'          => TRUE
                        )
                    );
                
                $estiloTituloColumnas = array(
                    'font' => array(
                        'name'      => 'Arial',
                        'bold'      => true,                          
                        'color'     => array(
                            'rgb' => 'FFFFFF'
                            )
                        ),
                    'fill'  => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        //'type'      => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'color' => array(
                            'rgb' => 'E51937'
                            )
                        ),
                    'borders' => array(
                        'top'     => array(
                            'style' => PHPExcel_Style_Border::BORDER_MEDIUM/* ,
                            'color' => array(
                                'rgb' => '143860'
                                )*/
                            ),
                        'bottom'     => array(
                            'style' => PHPExcel_Style_Border::BORDER_MEDIUM/* ,
                            'color' => array(
                                'rgb' => '143860'
                                )*/
                            )
                        ),
                    'alignment' =>  array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'wrap'          => TRUE
                        ));

                $estiloInformacion = new PHPExcel_Style();
                $estiloInformacion->applyFromArray(
                    array(
                        'font' => array(
                            'name'      => 'Arial',               
                            'color'     => array(
                                'rgb' => '000000'
                                )
                            ),
                        'fill'  => array(
                            'type'      => PHPExcel_Style_Fill::FILL_SOLID/*,
                            'color'     => array('argb' => 'FFd9b7f4')*/
                            ),
                        'borders' => array(
                            'left'     => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN /*,
                                'color' => array(
                                    'rgb' => '3a2a47'
                                    )*/
                                )             
                            )
                        ));

                $objPHPExcel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($estiloTituloReporte);
                $objPHPExcel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($estiloTituloColumnas);       
                $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:N".($i-1));
                
                for($i = 'A'; $i <= 'N'; $i++){
                    $objPHPExcel->setActiveSheetIndex(0)            
                    ->getColumnDimension($i)->setAutoSize(TRUE);
                }

                // Se asigna el nombre a la hoja
                $objPHPExcel->getActiveSheet()->setTitle($this->text['objectives']);

                // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
                $objPHPExcel->setActiveSheetIndex(0);
                 // Inmovilizar paneles 
                //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
                $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

                // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'.$this->text['obj_report'].'.xlsx"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
                exit;
            }
        }

    }

}
?>
