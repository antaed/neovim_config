<?php
class Controller_fisetransfer extends Controller {

    public function index($args=array()) {
        if (!$this->user_can('fisetransfer/acceseaza')) { $this->router->import('404'); return; }
        $model = new GestiuneActe($this->registry);

        $type = 5;

        $modelParteneri = new Partners($this->registry);
                $opFurnizori = $modelParteneri->getArray(array( 'type'=>4 ));
                $opClienti = isset($_GET['id_furnizor']) && $_GET['id_furnizor'] ? $modelParteneri->getArray(array( 'type'=>0, 'id'=>$_GET['id_furnizor'] )) : array();
        $modelGestiune = new Gestiuni( $this->registry ); $opGestiuni = $modelGestiune->getArray( array( 'id_firma' => array_keys($opFurnizori) ) );
        $modelConturi = new ConturiContabile($this->registry); $conturi = $modelConturi->getArray(array('options'=>1));

        $filtre = array();
        $filtre['id_gestiune'] = new FormElement( array( 'name'=>'id_gestiune', 'class'=>'form-control s2', 'style'=>'width:100%', 'value'=>'0', 'type'=>'select'), array('list'=>array($this->lng['txtToate']) + $opGestiuni, 'label'=>$this->lng['txtGestiunea'], 'columns'=>'col-xs-12 col-sm-4 col-md-2' ) );
        $filtre['status'] = new FormElement( array( 'name'=>'status', 'value'=>'0', 'type'=>'select'), array( 'label'=>$this->lng['txtStatus'], 'list'=>array($this->lng['txtToate']) + $model->opStatus, 'columns'=>'col-xs-12 col-sm-4 col-md-2' ) );
        $filtre['id_gestiune'] = new FormElement( array( 'name'=>'id_gestiune', 'value'=>'0', 'type'=>'select'), array( 'label'=>$this->lng['txtGestiunea'], 'list'=>array($this->lng['txtToate']) + $opGestiuni, 'columns'=>'col-xs-12 col-sm-4 col-md-2' ) );
        $filtre['nr_char'] = new FormElement( array( 'name'=>'nr_char', 'value'=>'', 'type'=>'text', 'placeholder'=>$this->lng['txtNrBC']), array( 'label'=>$this->lng['txtNrBC'], 'columns'=>'col-xs-12 col-sm-4 col-md-2' ) );
        $filtre['date>='] = new FormElement( array( 'name'=>'dela', 'value'=>'', 'type'=>'date'), array( 'label'=>$this->lng['txtDeLa'], 'columns'=>'col-xs-12 col-sm-4 col-md-2' ) );
        $filtre['date<='] = new FormElement( array( 'name'=>'panala', 'value'=>'', 'type'=>'date'), array( 'label'=>$this->lng['txtPanaLa'], 'columns'=>'col-xs-12 col-sm-4 col-md-2' ) );
        if (isset($_GET['id_anexa'])) {
            $filtre['id_anexa'] = new FormElement( array( 'name'=>'id_anexa', 'value'=>$_GET['id_anexa'], 'type'=>'hidden'), array() );
        }

        $action = $this->router->currentPage;

        $op = array('type'=>$type, 'link_invoices'=>true ,'gestiune_sursa'=>true);
        foreach ($filtre as $key=>$formElement) { if($formElement->get('GET') && $formElement->value) { $action .= '&'.$formElement->name.'='.$formElement->value; $op[$key] = $formElement->value; } }

        $canExport = $this->user_can('fisetransfer/export');
        if (isset($_GET['export'])) {
            if ($canExport) {
                if (isset($_GET['ids'])) { $op = array( 'id' => explode(',', $_GET['ids'])); }
                $objectsSet = $model->get($op); $csvArray=array();
                if ($objectsSet) {
                    $excel=new DTExcel($this->registry);
                    $csvArray[]=array('ID', 'Nr', 'Data', 'Gestiunea curenta', 'Gestiunea sursa', 'Incadrarea', 'Valoare fara TVA', 'Valoare TVA', 'Valoare cu TVA', 'Moneda', 'Status', 'Nr data FF');
                    foreach ($objectsSet as $row) {
                        $csvArray[]=array($row->id, $row->nr_char, cleanDate($row->date), $row->gestiune, $row->gestiune_sursa, $conturi[$row->id_incadrare], $row->valoare, $row->taxa, $row->total, $row->moneda, $model->opStatus[$row->status], $row->nr_data_invoice);
                    }
                    $excel->exportArray($csvArray, 'FT', 'FT', 'Sheet1'); die();
                } else {
                    $this->messages->set( array('type'=>'notify', 'message'=>$this->lng['txtNuSuntFiseDeTransferDeExportat'], 'className'=>'danger') );
                    $this->router->redirect( $this->router->url( $action . (isset($args['page']) ? '&page='.(int)$args['page'] : '') ) );
                }
            } else {
                $this->messages->set( array('type'=>'notify', 'message'=>$this->lng['txtNuAiPermisiuneaSaExportiFiseDeTransfer'], 'className'=>'danger') );
                $this->router->redirect( $this->router->url( $action . (isset($args['page']) ? '&page='.(int)$args['page'] : '') ) );
            }
        }

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = 50;

        $objectsCount = $model->count($op);
        $pagination = new Pagination($objectsCount, $perPage, $page);
        $objectsSet = $model->get($op, $pagination->onthispage, $pagination->offset);
        $filtreaza = $model->get(array('type'=>array(2,4)));$bonuri=array();
        foreach($filtreaza as $obj){
            $bonuri[$obj->id] = $obj;
        }

        $this->template->paginationURL = $this->router->url($action.'&page=');
        if ($page > 1) { $action .= "&page=".$page; }

        if (!$this->authUser->master_id) {
            $modelMasters = new Masters($this->registry);
            $this->template->opMaster = $modelMasters->getArray();
        }

        $modelINV = new Invoices($this->registry);
        foreach ($objectsSet as $obj){
            $tip_factura = $modelINV->getOne(array('id'=>$obj->id_invoice));
            if ($tip_factura){$obj->invoice_type = $tip_factura->type;}
        }

        $this->template->pagination = $pagination;
        $this->template->filtre = $filtre;
        $this->template->page = $page;
        $this->template->action = $action;

        $this->template->bonuri = $bonuri;
        $this->template->objectsSet = $objectsSet;
        $this->template->objectsCount = $objectsCount;

        $this->template->firma = $modelParteneri->count(array( 'type'=>4 )) == 1 ? 0 : 1 ;
        $this->template->opStatus = $model->opStatus;
        $this->template->invoiceType = $modelINV->opType;
        $this->template->invoiceURL = $modelINV->typeURL;
        $this->template->opFurnizori = $opFurnizori;
        $this->template->opConturi = $conturi;

        $this->template->canExport = $canExport;
        $this->template->exportXlsxUrl = $this->router->url($action.'&export=1');

        $this->template->breadcrumbs = array(
            array('title' => '<i class="fa fa-dashboard"></i> ' . $this->registry->lng['txtAcasa'], 'href'=>$this->router->url()),
            array('title' => $this->lng['txtFiseDeTransfer'], 'href'=>$this->baseUrl.$this->router->currentPage),
        );

        $this->template->menuIndex = 95;

        $this->template->htmlTitle = APP_NAME.' | '.$this->lng['txtFiseDeTransfer'];
        $this->template->htmlCanonical = '';
        $this->template->htmlDescription = $this->lng['txtFiseDeTransfer'].'.';
        $this->template->setFromFile('fisetransfer');
    }

    public function edit($args=array()) {
        $type = 5;

        $model = new GestiuneActe($this->registry);
        $modelEntries = new GestiuneTranzactii($this->registry);
        $modelParteneri = new Partners($this->registry);
        $modelConturi = new ConturiContabile($this->registry); $conturi = $modelConturi->getArray(array('options'=>1));

        $idFT = isset($_GET['id']) ? $_GET['id'] : false;
        $ft = $idFT ? $model->getOne( array( 'id' => $idFT, 'type'=>$type, 'gestiune_sursa'=>1) ) : false;

        if (!$ft) { $this->router->import('404'); return; }
        $this->registry->infoObject = $ft;

        $ft->curs_valutar = $this->cursValutar;

        $canClone = false; $bf = false; $invoice = false;
        $modelBF = new BonuriFiscale($this->registry); $bf = $modelBF->getOne( array( 'id_bon_marfa' => $ft->id ) );
        if (!$bf || !$ft->id_invoice) { $canClone = true; }
        if (!$bf && $ft->id_invoice) { $bf = $modelBF->getOne(array('id_bon_marfa' => $ft->id_invoice)); }

        $opFirme = $modelParteneri->getArray(array('id'=>$ft->id_firma));

        $action = $this->router->currentPage.'&id='.$idFT;

        $op = array('id_gestiune_act'=>$idFT, 'ignore'=>0);

        $canExport = $this->user_can('fisetransfer/export');
        if (isset($_GET['export'])) {
            if ($canExport) {
                if (isset($_GET['ids'])) { $op = array( 'id' => explode(',', $_GET['ids'])); }
                $objectsSet = $modelEntries->get($op); $csvArray=array();
                if ($objectsSet) {
                    $excel=new DTExcel($this->registry);
                    $csvArray[]=array('ID', 'Incadrarea', 'Cod', 'Denumire', 'Serie', 'Lot', 'Sarja', 'Cantitate consumata', 'UM', 'Pret unitar', 'Valoare', 'Cota TVA', 'Valoare TVA', 'Total cu TVA', 'Moneda', 'Stoc' );
                    foreach ($objectsSet as $row) {
                        $csvArray[]=array( $row->id, $conturi[$row->id_incadrare], $row->code, $row->title, $row->serie, $row->lot, $row->sarja, $row->buc, $this->um[$row->um], $row->pret_unitar, $row->valoare, $row->tva, $row->taxa, $row->total, $row->moneda, $row->stoc);
                    }
                    $excel->exportArray($csvArray, 'FT_'.$ft->nr_char, 'ft', 'Sheet1'); die();
                } else {
                    $this->messages->set( array('type'=>'notify', 'message'=>$this->lng['txtNuSuntProduseSiServiciiDeExportat'], 'className'=>'danger') );
                    $this->router->redirect( $this->router->url( $action . (isset($args['page']) ? '&page='.(int)$args['page'] : '') ) );
                }
            } else {
                $this->messages->set( array('type'=>'notify', 'message'=>$this->lng['txtNuAiPermisiuneaSaExportiBonulDeMarfa'], 'className'=>'danger') );
                $this->router->redirect( $this->router->url( $action . (isset($args['page']) ? '&page='.(int)$args['page'] : '') ) );
            }
        }

        $rows = $modelEntries->get($op);
        $objectsSet = array();
        foreach ($rows as $row) {
            if ($row->group_id) {
                if (isset($objectsSet[$row->group_id])) {
                    $objectsSet[$row->group_id]->buc += $row->buc; $objectsSet[$row->group_id]->stoc += $row->stoc; $objectsSet[$row->group_id]->valoare += $row->valoare; $objectsSet[$row->group_id]->taxa += $row->taxa; $objectsSet[$row->group_id]->total += $row->taxa + $row->valoare;
                } else {
                    $objectsSet[$row->group_id] = $row;
                }
            } else {
                $objectsSet[$row->id] = $row;
            }
        }

        $taxe = array();
        foreach ($objectsSet as $row) { $taxe[$row->tva] = isset($taxe[$row->tva]) ? $taxe[$row->tva] + cConvert($row->taxa, $row->moneda, $ft->moneda, $ft->curs_valutar) : cConvert($row->taxa, $row->moneda, $ft->moneda, $ft->curs_valutar); }

        $count = $modelEntries->count(array('id_gestiune'=>$ft->id_gestiune_sursa, 'pe_stoc'=>1, 'ignore'=>0));

        $this->template->paginationURL = $this->router->url($action.'&page=');
        $this->template->tab = isset($_GET['tab']) ? (int)$_GET['tab'] : 1;

        $this->template->action = $action;
        $this->template->count = $count;

        $this->template->opStatus = $model->opStatus;
        $this->template->objectsSet = $objectsSet;
        $this->template->object = $ft;
        $this->template->taxe = $taxe;
        $this->template->opFirme = $opFirme;
        $this->template->opConturi = $conturi;
        $this->template->bf = $bf;
        $this->template->status = $ft->status!=2 ? true : false;
        $this->template->canClone = $canClone;

        $this->template->canExport = $canExport;
        $this->template->exportXlsxUrl = $this->router->url($action.'&export=1');

        $this->template->breadcrumbs = array(
            array('title' => '<i class="fa fa-dashboard"></i> ' . $this->registry->lng['txtAcasa'], 'href'=>$this->router->url()),
            array('title' => $this->lng['txtFiseDeTransfer'], 'href'=>$this->baseUrl.'fisetransfer/'),
            array('title' =>  $this->lng['txtFiseDeTransfer'].' '.$this->lng['txtNnr'].$ft->nr_char, 'href'=>$this->baseUrl.$this->router->currentPage.'&id='.$idFT),
        );

        $this->template->menuIndex = 95;

        $this->template->htmlTitle = APP_NAME.' | '.$this->lng['txtFisaDeTransfer'].' '.$this->lng['txtNnr'].$ft->nr_char;
        $this->template->htmlCanonical = '';
        $this->template->htmlDescription = 'Dashboard 1';
        $this->template->setFromFile('fisetransfer_edit');
    }

    public function pdf($args=array()) {
        $type = 5;
        $modelFT = new GestiuneActe($this->registry);

        $idFT = isset($args['id']) ? $args['id'] : false;
        $object = $idFT ? $modelFT->getOne( array( 'id' => $idFT, 'type'=>$type, 'gestiune_sursa'=>1) ) : false;

        if (!$object) { $this->router->import('404'); return; }

        $modelFTEntries = new GestiuneTranzactii($this->registry); $produse = $modelFTEntries->get( array( 'id_gestiune_act'=>$object->id, 'ignore'=>0) );
        $modelConturi = new ConturiContabile($this->registry); $opConturi = $modelConturi->getArray(array('options'=>1));

        $taxe = array();
        foreach ($produse as $row) { $taxe[$row->tva] = isset($taxe[$row->tva]) ? $taxe[$row->tva] + cConvert($row->taxa, $row->moneda, $object->moneda, $this->cursValutar) : cConvert($row->taxa, $row->moneda, $object->moneda, $this->cursValutar); }

        $model = new Partners($this->registry); $unitatea = $model->getOne( array( 'id' => $object->id_firma, 'type'=>4 ) );

        $ean = str_pad(($object->id * $unitatea->master_id * 9857) % 9973, 4, '0', STR_PAD_LEFT) . str_pad($object->id, 8, '0', STR_PAD_LEFT);

        $infoBeneficiar = $object->info_beneficiar;

        $fInfo = $this->lng['txtGestiunea'].': '.$object->gestiune_sursa."\n";
        if(isset($object->responsabil_sursa) && $object->responsabil_sursa){$fInfo .= $this->lng['txtResponsabil'].': '.$object->responsabil_sursa."\n";}
        $fInfo .= "\nSemnatura:\n";

        $fInfo = strtoupper(htmlspecialchars_decode($fInfo));

        $cInfo = $this->lng['txtGestiunea'].': '.$object->gestiune."\n";
        if(isset($object->responsabil) && $object->responsabil){$cInfo .= $this->lng['txtResponsabil'].': '.$object->responsabil."\n";}
        $cInfo .= "\n".$this->lng['txtSemnatura'].":\n";

        $cInfo = strtoupper(htmlspecialchars_decode($cInfo));

        $expeditia="";

        $pdf=new MyUTFPDF($this->registry); $pdf->AddPage('L'); $pdf->SetDisplayMode('fullpage');
        $pdf->SetFillColor(255,255,255); $pdf->SetDrawColor(160,160,160);
        $pdf->SetMargins(18,18); $pdf->SetXY(18,18);

        if ($unitatea->file_name) { $pdf->Image('images/db/'.$unitatea->file_name.'.png',128.5, 18, 40); }
        $maxY = 0;
        $pdf->SetFont('Roboto', 'B', 10); $pdf->Cell(130.5, 10, strtoupper($unitatea->name), 0, 1);

        $pdf->SetFont('Roboto', 'B', 9); $pdf->Cell(130.5, 5, 'GESTIUNEA SURSA', 0, 0); $pdf->Cell(130.5, 5, 'GESTIUNEA DESTINATIE', 0, 1, 'R');

        $tmpY = $pdf->GetY();
        $pdf->SetFont('RobotoCondensed', '', 8); $pdf->MultiCell(130.5, 3.5, $fInfo, 0, 'L'); $maxY = max($pdf->GetY(), 0);

        $pdf->SetXY(148.5, $tmpY);
        $pdf->SetFont('RobotoCondensed', '', 8); $pdf->MultiCell(130.5, 3.5, $cInfo, 0, 'R');  $maxY = max($pdf->GetY(), $maxY);

        $pdf->SetXY(18, max($maxY, 50) + 3);

        $pdf->SetFont('RobotoCondensed', '', 22);
        $pdf->SetTextColor(0, 104, 158); $pdf->Cell(261, 7, 'FISA DE TRANSFER', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0); $pdf->SetFont('Roboto', 'B', 10);
        $pdf->Cell(261, 7, 'Nr. '.trim($object->nr_char).$this->lng['txtDin'].cleanDate($object->date), 0, 1, 'C');

        //tabel obiecte
        $pdf->SetXY(18 , $pdf->GetY()+3); $pdf->SetFont('RobotoCondensed', '', 8);

        if (count($taxe)==1) { $pdf->Cell(261,8,'Cota TVA: '.key($taxe).'%',0, 1 ,'R'); }

        $pdf->Cell(10+86+15, 8, '', 'TRL', 0); $pdf->Cell(15+20+20+20, 8, 'ACHIZITIE', 'TRL', 0, 'C'); $pdf->Cell(15+20+20+20, 8, 'VANZARE', 'TRL', 1, 'C');


        $pdf->MultiCell(10,8,'Nr. crt','TLRB','C',true); $pdf->SetXY($pdf->GetX()+10,$pdf->GetY()-8);
        $pdf->Cell(86,8,'Denumirea produselor','TLRB',0,'C',true); //131
        $pdf->Cell(15,8,'U.M.','TLRB',0,'C',true);

        $pdf->MultiCell(15,4,"Cant.\nrecept.",'TLRB','C',true); $pdf->SetXY(144,$pdf->GetY()-8);
        $pdf->MultiCell(20,4,"Pret unitar\n-".$object->moneda."-",'TLRB','C',true); $pdf->SetXY(164,$pdf->GetY()-8);
        $pdf->MultiCell(20,4,"Valoare\n-".$object->moneda."-",'TLRB','C',true);$pdf->SetXY(184,$pdf->GetY()-8);
        $pdf->MultiCell(20,4,"TVA\n-".$object->moneda."-",'TLRB','C',true);$pdf->SetXY(204,$pdf->GetY()-8);

        $pdf->MultiCell(15,4,"Marja.\nprofit",'TLRB','C',true); $pdf->SetXY(219,$pdf->GetY()-8);
        $pdf->MultiCell(20,4,"Pret unitar\n-vanzare-",'TLRB','C',true); $pdf->SetXY(239,$pdf->GetY()-8);
        $pdf->MultiCell(20,4,"Valoare\n-vanzare-",'TLRB','C',true);$pdf->SetXY(259,$pdf->GetY()-8);
        $pdf->MultiCell(20,4,"TVA\n-vanzare-",'TLRB','C',true);

        $cy=$pdf->GetY();
        $i=1;
        $pdf->SetTextColor(0,0,0); $pdf->SetLineWidth(0.3);

        $pageNo = $pdf->pageNo;

        $pdf->Cell(10, 2, '', 'RL', 0); $pdf->Cell(86, 2, '', 'RL', 0);$pdf->Cell(15, 2, '', 'RL', 0);$pdf->Cell(15, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0); $pdf->Cell(15, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 1);

        $object->valoare_vanzare = 0; $object->taxa_vanzare = 0;
        foreach ($produse as $key=>$entry) {
            $valoareVanzare = number_format(cConvert($entry->pret_unitar_vanzare * $entry->buc, $entry->moneda, $object->moneda, $this->cursValutar), NR_DECIMALS, '.', ''); $object->valoare_vanzare += $valoareVanzare;
            $taxaVanzare = number_format(cConvert($entry->taxa_vanzare * $entry->buc, $entry->moneda, $object->moneda, $this->cursValutar), NR_DECIMALS, '.', ''); $object->taxa_vanzare += $taxaVanzare;

            $pdf->SetX(28);
            $pdf->MultiCell(86,4,htmlspecialchars_decode($entry->title).( $entry->code || $entry->serie ? "\n".htmlspecialchars_decode('Cod: '.$entry->code.', Serie: '.$entry->serie) : '' ).($entry->id_incadrare ? "\n".htmlspecialchars_decode($opConturi[$entry->id_incadrare]) : ''),'LR','L');

            if ($pdf->pageNo != $pageNo) { $pageNo = $pdf->pageNo;
                $ch=$pdf->GetY()-10; $cy = $ch+10;
                $pdf->SetXY(18,10);
            } else {
                $ch=$pdf->GetY()-$cy; $cy=$pdf->GetY();
                $pdf->SetXY(18,$cy-$ch);
            }

            $pdf->Cell(10,$ch,$i,'RL',0,'C'); $pdf->SetX(114);
            $pdf->Cell(15,$ch,$this->registry->um[$entry->um],'LR',0,'C');

            $pdf->Cell(15,$ch,$entry->buc,'LR',0,'C');
            $pdf->Cell(20,$ch,dtNumberFormat(cConvert($entry->pret_unitar, $entry->moneda, $object->moneda, $this->cursValutar)),'LR',0,'C');
            $pdf->Cell(20,$ch,dtNumberFormat(cConvert($entry->valoare, $entry->moneda, $object->moneda, $this->cursValutar)),'LR',0,'C');
            $pdf->Cell(20,$ch,dtNumberFormat(cConvert($entry->taxa, $entry->moneda, $object->moneda, $this->cursValutar)),'LR',0,'C');

            $pdf->Cell(15,$ch,$entry->marja,'LR',0,'C');
            $pdf->Cell(20,$ch,dtNumberFormat(cConvert($entry->pret_unitar_vanzare, $entry->moneda, $object->moneda, $this->cursValutar)),'LR',0,'C');
            $pdf->Cell(20,$ch,dtNumberFormat($valoareVanzare),'LR',0,'C');
            $pdf->Cell(20,$ch,dtNumberFormat($taxaVanzare),'LR',1,'C');

            $pdf->Cell(10, 2, '', 'RL', 0); $pdf->Cell(86, 2, '', 'RL', 0); $pdf->Cell(15, 2, '', 'RL', 0); $pdf->Cell(15, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0); $pdf->Cell(15, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 0);$pdf->Cell(20, 2, '', 'RL', 1);
            $i++;
        }

        $pdf->Cell(10,2,' ','RBL',0,'C');
        $pdf->Cell(86,2,' ','RBL',0,'C');
        $pdf->Cell(15,2,' ','BLR',0,'C');
        $pdf->Cell(15,2,' ','BLR',0,'C');
        $pdf->Cell(20,2,' ','BLR',0,'C');
        $pdf->Cell(20,2,' ','BLR',0,'C');
        $pdf->Cell(20,2,' ','BLR',0,'C');

        $pdf->Cell(15,2,' ','BLR',0,'C');
        $pdf->Cell(20,2,' ','BLR',0,'C');
        $pdf->Cell(20,2,' ','BLR',0,'C');
        $pdf->Cell(20,2,' ','BLR',1,'C');

        $pdf->Cell(146,10,'Total     ','LR',0,'R'); $pdf->Cell(20,10,dtNumberFormat($object->valoare),'LR',0,'C'); $pdf->Cell(20,10,dtNumberFormat($object->taxa),'LR',0,'C');
        $pdf->Cell(35,10,'','LR',0,'C'); $pdf->Cell(20,10,dtNumberFormat($object->valoare_vanzare),'LR',0,'C'); $pdf->Cell(20,10,dtNumberFormat($object->taxa_vanzare),'LR',1,'C');

        $pdf->SetFont('Roboto','',12); $pdf->SetTextColor(0,0,0); $pdf->SetFillColor(240);$pdf->SetFont('Arial','B',12);
        $pdf->Cell(146,10,'Total','T',0,'R',true); $pdf->Cell(40,10,dtNumberFormat($object->total).' '.$object->moneda,'T',0,'C',true); $pdf->Cell(75,10,dtNumberFormat($object->valoare_vanzare + $object->taxa_vanzare).' '.$object->moneda,'T',1,'R',true);

        if (isset($args['curs']) && isset($object->cursValutar[$args['curs']])) { $pdf->SetFont('Roboto', 'B', 9.5); $pdf->Cell(150, 6, 'CURS VALUTAR: 1 '.$args['curs'].' ='.$object->cursValutar[$args['curs']].' RON', 0, 1, 'L'); }

        $fileName = 'FT_'.urlFromTitle($object->nr_char).'_'.$object->id.'.pdf';
        if (isset($args['save_local'])) {
            $pdf->Output(ATT_DIR.$fileName, 'F');
            return ATT_DIR.$fileName;
        } else {
            $pdf->Output($fileName, 'I');
        }
        die();
    }

    public function pdf_etichete($args=array()) {
        $modelFT = new GestiuneActe($this->registry);
        include 'ean13/ean_parity.php';

        $idNIR = isset($args['id']) ? $args['id'] : false;
        $object = $idNIR ? $modelFT->getOne( array( 'id' => $idNIR, 'type'=>5 ) ) : false;

        if (!$object) { $this->router->import('404'); return; }

        $modelFTEntries = new GestiuneTranzactii($this->registry);

        $idItem = isset($_GET['id_item']) ? $_GET['id_item'] : 0;
        $idGroup = isset($_GET['group_id']) && is_numeric($_GET['group_id']) ? $_GET['group_id'] : 0;

        $op = array( 'id_gestiune_act'=>$object->id, 'ignore'=>0 );
        if ($idGroup) { $op['group_id'] = $idGroup; } elseif($idItem) { $op['id'] = $idItem; }

        $produse = $modelFTEntries->get($op);
        if (!$produse) { echo $this->lng['txtNuSuntProdusePeFisaDeTransfer']; die(); }

        $model = new Partners($this->registry);
        $unitatea = $model->getOne( array( 'id' => $object->id_firma, 'type'=>4 ) );

        $infoFurnizor = $object->info_furnizor;

        $pdf=new MyUTFPDF($this->registry);
        $pdf->showPageNum =false;
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Roboto', '', 7);


        $pdf->SetAutoPageBreak(true, 1);

        foreach ($produse as $key=>$entry) {
            if ($entry->serie) {
                $serie = is_numeric($entry->serie) && strlen($entry->serie)<=13 ? str_pad($entry->serie, 12, '0', STR_PAD_LEFT) : '010' . str_pad($entry->id, 9, '0', STR_PAD_LEFT);
            } else {
                $serie = '010' . str_pad($entry->id, 9, '0', STR_PAD_LEFT);
                $serie .= ean_checksum($serie);
                $entry->serie = $serie; $modelFTEntries->save($entry);
            }
            $pdf->AddPage('L', array(38.1, 25.4)); $pdf->SetMargins(1,1);$pdf->Footer();
            $pdf->SetXY(1,1); $pdf->Image('http://dtwebdesign.ro/ean13/'.$serie.'x4.png', 1, 1, 36);
            $pdf->SetXY(1,1); $pdf->SetFont('RobotoCondensed', '', 8); $pdf->Cell(18, 2.4,'F'.$object->id_furnizor, '', 0, '', true); $pdf->Cell(18, 2.4,cleanDate($object->date), '', 1, 'R', true);
            $pdf->SetXY(1,13);
            $pdf->SetFont('RobotoCondensed', '', 7); $pdf->MultiCell(36, 2.5, $entry->title, '', 'L');
            $pdf->SetFont('RobotoCondensed', '', 9); $pdf->Cell(36, 3, dtNumberFormat(cuTVA(max($entry->pret_unitar, $entry->pret_unitar_vanzare), $entry->tva)).' '.str_replace('RON', 'LEI', $entry->moneda), '', 1);

            $pdf->SetXY(1,21); $pdf->SetFont('RobotoCondensed', '', 7); $pdf->Cell(36, 2.4, 'www.computer-line.ro', '', 1, 'C');
        }

        $pdf->SetDisplayMode('fullpage');

        $fileName = 'ETICHETE_FT_'.urlFromTitle($object->nr_char).'_'.$object->id.'.pdf';
        if (isset($args['save_local'])) {
            $pdf->Output(ATT_DIR.$fileName, 'F');
            return ATT_DIR.$fileName;
        } else {
            $pdf->Output($fileName, 'I');
        }
        die();
    }

    public function modal($args=array()) {
        $this->template->addHeader('Content-Type: application/json');
        $action = isset($_GET['action']) ? $_GET['action'] : false;
        switch ($action) {
            case 'sterge' : $result = $this->modal_sterge(); break;
            case 'edit' : $result = $this->modal_edit(); break;
            case 'adauga' : $result = $this->modal_adauga(); break;
            case 'bulk-sterge' : $result = $this->modal_bulk_sterge(); break;
            case 'import-xlsx' : $result = $this->modal_import_xlsx(); break;

            case 'produse-adauga' : $result = $this->modal_produse_adauga(); break;
            case 'produse-bulk-adauga' : $result = $this->modal_produse_bulk_adauga(); break;
            case 'produs-sterge' : $result = $this->modal_produs_sterge(); break;

            case 'serii-import' : $result = $this->modal_serii_import(); break;
            case 'serii-edit' : $result = $this->modal_serii_edit(); break;
            case 'serii-genereaza' : $result = $this->modal_serii_genereaza(); break;

            case 'produse-view' : $result = $this->modal_produse_view(); break;
            case 'produs-view' : $result = $this->modal_produs_view(); break;

            case 'status' : $result = $this->modal_status(); break;
            case 'bulk-status' : $result = $this->modal_bulk_status(); break;

            default : $result = array( 'error'=>1, 'errorMessage'=>$this->registry->lng['txtFunctiaSolicitataNuMomentan'], 'html'=>'', 'js' => '' );
        }
        $this->template->setContent(json_encode($result));
    }

    public function action($args=array()) {
        $this->template->addHeader('Content-Type: application/json');
        $action = isset($_GET['action']) ? $_GET['action'] : ( isset($_POST['action']) ? $_POST['action'] : false);

        switch($action) {
            case 'sterge' : $result = $this->action_sterge(); break;
            case 'edit' : $result = $this->action_edit(); break;
            case 'adauga' : $result = $this->action_adauga(); break;
            case 'bulk-sterge' : $result = $this->action_bulk_sterge(); break;
            case 'import-xlsx' : $result = $this->action_import_xlsx(); break;

            case 'produse-adauga' : $result = $this->action_produse_adauga(); break;
            case 'produse-bulk-adauga' : $result = $this->action_produse_bulk_adauga(); break;
            case 'produs-sterge' : $result = $this->action_produs_sterge(); break;

            case 'serii-import' : $result = $this->action_serii_import(); break;
            case 'serii-edit' : $result = $this->action_serii_edit(); break;
            case 'serii-genereaza' : $result = $this->action_serii_genereaza(); break;

            case 'status' : $result = $this->action_status(); break;
            case 'bulk-status' : $result = $this->action_bulk_status(); break;

            default : $result = array( 'error'=>2, 'errorMessage'=>$this->registry->lng['txtFunctiaSolicitataNuMomentan'], 'html'=>'', 'js' => '' );
        }
        $this->template->setContent(json_encode($result));
    }

    /**
     * STERGE FISA DE TRANSFER
    */
    private function modal_sterge() {
        $type = 5;
        if (!$this->user_can('fisetransfer/sterge') || $this->authUser->role < 100) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuAiDrepturiSuficienteSaStergiFiseDeTransfer'], 'html'=>'', 'js' => '' ); }

        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $model = new GestiuneActe($this->registry); $row = $model->getOne( array( 'id' => $id, 'type'=>$type ) );
        if (!$row) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => '' ); }

        $vars = array(
            'confirmTxt' => $this->lng['txtFisaDeTransfer'].' <strong>'.$row->nr_char.$this->lng['txtDin'].cleanDate($row->date).'</strong> '.$this->lng['txtVaFiStearsaSiNuVaMaiPuteaFiRecuperataEstiSigur'],
            'url' => $this->router->url('fisetransfer-action/'),
            'actionMethod' => $_GET['action'],
            'id' => $row->id,
            'tk' => scramble($this->authUser->id.'del_bc'.$row->id),
        );
        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_sterge', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_sterge($id=false) {
        $type = 5;
        if (!$this->user_can('fisetransfer/sterge')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuAiDrepturiSuficienteSaStergiFiseDeTransfer'], 'html'=>'', 'js' => '' ); }

        if (!$id) { $id = isset($_GET['id']) && isset($_GET['tk']) && ($_GET['tk'] == scramble($this->authUser->id.'del_bc'.$_GET['id'])) ? $_GET['id'] : false; }
        if (!$id) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtTokenInvalid'], 'html'=>'', 'js' => '' ); }

        $model = new GestiuneActe($this->registry); $row = $model->getOne( array( 'id' => $id, 'type'=>$type ) );
        if (!$row) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => '' ); }
        $modelGT = new GestiuneTranzactii($this->registry); $produse = $modelGT->getArray(array('id_gestiune_act'=>$row->id, 'sort'=>'t1.`id` DESC'));

        foreach($produse as $key=>$produs) {
            $deSters = $modelGT->getOne(array('id'=>$key));
            $modelGT->delete($deSters);
        }

        $model->delete( $row );
        $message = "Fisa de transfer ".$row->nr_char." ".$this->lng['txtAFostStearsaCuSucces'];
        $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') );
        return array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => 'window.location.reload()' );
    }

    /**
     * BULK STERGE
    */
    private function modal_bulk_sterge() {
        $type = 5;
        if (!$this->user_can('fisetransfer/sterge')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuAiDrepturiSuficienteSaStergiFiseDeTransfer'], 'html'=>'', 'js' => '' ); }

        $id = isset($_GET['id']) ? explode(',',$_GET['id']) : 0;
        $model = new GestiuneActe($this->registry); $rows = $model->get( array( 'id' => $id , 'type'=>$type) );

        if (!$rows) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtTrebuieSaSelecteziMinimOFisaDeTransfer'], 'html'=>'', 'js' => '' ); }
        $ids = array(); foreach ($rows as $row) { $ids[] = $row->id; } $ids = implode(',', $ids);

        $vars = array(
            'confirmTxt' =>$this->lng['txtFiseleDeTransferSelectateVorFiSterseSiNuVorMaiPuteaFiRecuperateEstiSigur'],
            'url' => $this->router->url('fisetransfer-action/'),
            'actionMethod' => $_GET['action'],
            'id' => $ids,
            'tk' => scramble($this->authUser->id.'delete_ft_bulk'.$ids),
        );
        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_sterge', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_bulk_sterge() {
        $type = 5;
        if (!$this->user_can('fisetransfer/sterge')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuAiPermisiuneaSaExportiFiseDeTransfer'], 'html'=>'', 'js' => '' ); }

        $id = isset($_GET['id']) && isset($_GET['tk']) && ($_GET['tk'] == scramble($this->authUser->id.'delete_ft_bulk'.$_GET['id'])) ? $_GET['id'] : 0;
        if (!$id) { return array( 'error'=>1, 'html'=>'', 'errorMessage'=>$this->lng['txtAAparutOEroareInternaIncercati'], 'js' => '' ); }

        $model = new GestiuneActe($this->registry); $rows = $model->get( array( 'id' => explode(',',$id), 'type'=>$type ) );
        if (!$rows) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtFiseleDeTransferSelectateNuExistaInBazaDeDate'], 'html'=>'', 'js' => '' ); }

        foreach ($rows as $row) {
            $modelGT = new GestiuneTranzactii($this->registry);$produs = $modelGT->getOne(array('id_gestiune_act'=>$row->id, 'sort'=>'t1.`id` DESC'));
            while($produs){
                $modelGT->delete($produs);
                $produs = $modelGT->getOne(array('id_gestiune_act'=>$row->id, 'sort'=>'t1.`id` DESC'));
            }
            $model->delete( $row );
        }
        $message = $this->lng['txtFisiereleDeTransferAuFostSterseCuSucces'];
        $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') );
        return array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => 'window.location.reload()' );
    }

    /**
     * ADAUGA
    */
    private function modal_adauga() {
        $type = 5;
        if (!$this->user_can('fisetransfer/adauga')) { return array('error'=>1, 'errorMessage'=>$this->lng['txtNuPotiAdaugaFiseDeTransfer'], 'html'=>'', 'js' => ''); }

        $model = new Partners($this->registry); $opFirme = $model->getArray(array('type' => 4)); $implFirma = key($model->getArray(array('type'=>4, 'options'=>11)));
        $modelGestiuni = new Gestiuni($this->registry); $opGestiuni = $modelGestiuni->getArray(array('id_firma'=>$implFirma));
        $model = new ConturiContabile($this->registry); $opConturi = $model->getArray(array('options'=>1));
        $model = new SeriiDocumente($this->registry); $rows = $model->get( array('type'=>$type+10, 'id_furnizor'=>$implFirma, 'status'=>1) );
        $modelFT = new GestiuneActe($this->registry); $opSerii = array(); foreach ($rows as $s) { $opSerii[$s->id] = $s->serie .$this->lng['txtSpaceNrSpace'].($modelFT->currentNr($type, $implFirma, $s->serie, $s->nr_min) + 1); }
        if (!$opSerii){return array('error'=>1, 'errorMessage'=>$this->lng['txtEroareNuEsteAdaugataSeriePentruFisaDeTransfer'], 'html'=>'', 'js' => '');}

        $fe = array();
        $fe['id_firma'] = new FormElement( array('name'=>'id_firma', 'id'=>'fe_id_firma', 'value'=>$implFirma, 'type'=>'select'), array( 'list'=>$opFirme, 'label'=>$this->lng['txtFirma'], 'columns'=>'col-xs-12' ) );
        $fe['id_gestiune_sursa'] = new FormElement( array( 'id'=>'id_gestiune_sursa', 'name'=>'id_gestiune_sursa', 'class'=>'form-control s2', 'data-action'=>'ajax-update', 'data-args'=>'target_id=fe_id_resp_sursa;method=resp_gestiune;id_gestiune=#id_gestiune_sursa', 'value'=>0, 'type'=>'select'), array( 'list'=>array($this->lng['txtAlege']) + $opGestiuni, 'label'=>$this->lng['txtGestiuneaSursa'], 'columns'=>'col-xs-12 col-md-6' ) );
        $fe['id_gestiune'] = new FormElement( array( 'id'=>'id_gestiune', 'name'=>'id_gestiune', 'class'=>'form-control s2', 'data-action'=>'ajax-update', 'data-args'=>'target_id=fe_id_resp;method=resp_gestiune;id_gestiune=#id_gestiune', 'value'=>0, 'type'=>'select'), array( 'list'=>array($this->lng['txtAlege']) + $opGestiuni, 'label'=>$this->lng['txtGestiuneaNoua'], 'columns'=>'col-xs-12 col-md-6' ) );

        $fe['id_resp_sursa'] = new FormElement( array( 'id'=>'fe_id_resp_sursa', 'name'=>'id_resp_sursa', 'class'=>'form-control s2', 'value'=>0, 'type'=>'select'), array( 'list'=>array($this->lng['txtResponsabil']), 'label'=>$this->lng['txtResponsabilGestiuneSursa'], 'columns'=>'col-xs-12 col-md-6' ) );
        $fe['id_resp'] = new FormElement( array( 'id'=>'fe_id_resp', 'name'=>'id_resp', 'class'=>'form-control s2', 'value'=>0, 'type'=>'select'), array( 'list'=>array($this->lng['txtResponsabil']), 'label'=>$this->lng['txtResponsabilGestiuneNoua'], 'columns'=>'col-xs-12 col-md-6' ) );

        $fe['id_serie'] = new FormElement( array('id'=>'id_serie', 'name'=>'id_serie', 'value'=>key($opSerii), 'type'=>'select'), array( 'list'=>$opSerii, 'label'=>$this->lng['txtSerieNr'], 'columns'=>'col-xs-12' ) );

        $fe['reload'] = new FormElement( array('name'=>'reload', 'type'=>'hidden', 'class'=>'', 'value'=>isset($_GET['reload']) && $_GET['reload']==1 ? 1 : 0) );


        $vars = array(
            'fe' => $fe,
            'actionMethod' => $_GET['action'],
            'modalTitle' => '<i class="fa fa-file-o"></i> '.$this->lng['txtAdaugaOFisaDeTransfer'],
            'modalCloseButton' => $this->lng['txtInchide'],
            'modalActionButton' => $this->lng['txtAdauga'],
            'modalBody' => '',
            'modalAlert' => '',
            'action' => $this->router->url('fisetransfer-action'),
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_add_ft', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_adauga() {
        $type = 5;
        if (!$this->user_can('fisetransfer/adauga')) { return array('error'=>2, 'errorMessage'=>$this->lng['txtNuPotiAdaugaFiseDeTransfer'], 'html'=>'', 'js' => ''); }
        $reload = isset($_POST['reload']) ? (int)$_POST['reload'] : 0;
        $idF = isset($_POST['id_firma']) ? $_POST['id_firma'] : 0;

        $model = new Partners($this->registry); $opFirme = $model->getArray(array('id' => $idF));
        $modelGestiuni = new Gestiuni($this->registry); $opGestiuni = $modelGestiuni->getArray(array('id_firma'=>$idF));
        if(!$opGestiuni){ return array('error'=>2, 'errorMessage'=>$this->lng['txtNuSetataGestiune'].$opFirme[$idF], 'html'=>'', 'js' => ''); }
        $model = new SeriiDocumente($this->registry); $seria = $model->getOne( array( 'id'=>$_POST['id_serie'], 'id_furnizor'=>$idF, 'type'=>$type+10 ) );
        $modelFT = new GestiuneActe($this->registry);

        $fe = array();
        $fe['id_firma'] = new FormElement( array('name'=>'id_firma', 'value'=>'', 'type'=>'select'), array( 'list'=>$opFirme, 'label'=>$this->lng['txtFirma'], 'columns'=>'col-xs-12' ) );
        $fe['id_gestiune_sursa'] = new FormElement( array( 'id'=>'id_gestiune_sursa', 'name'=>'id_gestiune_sursa', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array('list'=> $opGestiuni) );
        $fe['id_gestiune'] = new FormElement( array( 'id'=>'id_gestiune', 'name'=>'id_gestiune', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array('list'=> $opGestiuni) );

        $model = new Gestiuni($this->registry);
        $opUsers = $model->getUsers($_POST['id_gestiune_sursa']);
        if(!$opUsers){ return array('error'=>1, 'errorMessage'=>'', 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriNuEsteSetatResponsabilulPentruGestiuneaSursa'].'</p></div>', 'js' => '', 'error_list' =>'id_resp_sursa,id_gestiune_sursa'); }
        $fe['id_resp_sursa'] = new FormElement( array( 'id'=>'fe_id_resp_sursa', 'name'=>'id_resp_sursa', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array( 'requiered'=>1, 'list'=>$opUsers) );
        $opUsers = $model->getUsers($_POST['id_gestiune']);
        if(!$opUsers){ return array('error'=>1, 'errorMessage'=>'', 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriNuEsteSetatResponsabilulPentruGestiuneaSursa'].'</p></div>', 'js' => '', 'error_list' => 'id_resp,id_gestiune'); }
        $fe['id_resp'] = new FormElement( array( 'id'=>'fe_id_resp', 'name'=>'id_resp', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array( 'requiered'=>1, 'list'=>$opUsers));

        $errors= array();
        if($_POST['id_gestiune_sursa'] == $_POST['id_gestiune']){$errors[] = 'id_gestiune_sursa'; $errors[] = 'id_gestiune'; return array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriAtiAlesAceleasiGestiuni'].' </p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',', $errors) );}
        foreach ($fe as $formElement) { if(!$formElement->get()) { $errors[] = $formElement->name; }}

        if (!$errors) {
            $row = new TableRow();
            $row->type = $type;
            $row->id_firma = $idF;
            $row->serie = $seria->serie; $row->serie_min = $seria->nr_min;
            $row->status = 1;
            $row->date = $this->registry->date;
            foreach ($fe as $key=>$formElement) { $row->$key = $formElement->value; }
            $modelFT->save($row);

            if (!$errors) {
                $message = $this->lng['txtFisaDeTransferAFostAdaugata'];
                if ($reload) { $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') ); }
                $result = array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => $reload ? "window.location.href='".$this->router->url('fisetransfer-edit/&id='.$row->id)."'" : '' );
            } else {
                $result = array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriVaRugamCompletatiCampurileEvidentiate'].'</p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',', $errors) );
            }
        } else {
            $result = array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriVaRugamCompletatiCampurileEvidentiate'].'</p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',', $errors) );
        }
        return $result;
    }

    /**
     * EDIT
    */
    private function modal_edit() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => '' ); }
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $modelFT = new GestiuneActe($this->registry); $row = $modelFT->getOne( array( 'id' => $id, 'type'=>$type, 'gestiune_sursa'=>1) );

        if (!$row) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => '' ); }

        $model = new Partners($this->registry); $opFirme = $model->getArray(array('type' => 4));
        $modelGestiuni = new Gestiuni($this->registry); $opGestiuni = $modelGestiuni->getArray(array('id_firma'=>array_keys($opFirme)));
        $model = new SeriiDocumente($this->registry); $rows = $model->get( array('type'=>$type+10, 'id_furnizor'=>key($opFirme), 'status'=>1) );
        $model = new GestiuneTranzactii($this->registry); $produse = $model->get(array('id_gestiune_act'=>$id));

        $opSerii = array(); foreach ($rows as $s) { $opSerii[$s->id] = $s->serie .$this->lng['txtSpaceNrSpace'].($modelFT->currentNr($type, key($opFirme), $s->serie, $s->nr_min)); }
        $allOk = $produse ? false : true;

        $fe = array();



        if ($allOk){
            $fe['id_gestiune_sursa'] = new FormElement( array( 'id'=>'id_gestiune_sursa', 'name'=>'id_gestiune_sursa', 'class'=>'form-control s2', 'data-action'=>'ajax-update', 'data-args'=>'target_id=fe_id_resp_sursa;method=resp_gestiune;id_gestiune=#id_gestiune_sursa', 'value'=>'', 'type'=>'select'), array( 'list'=>array($this->lng['txtAlege']) + $opGestiuni, 'label'=>'Gestiunea sursa', 'columns'=>'col-xs-12 col-md-6' ) );
            $fe['id_gestiune'] = new FormElement( array( 'id'=>'id_gestiune', 'name'=>'id_gestiune', 'class'=>'form-control s2', 'data-action'=>'ajax-update', 'data-args'=>'target_id=fe_id_resp;method=resp_gestiune;id_gestiune=#id_gestiune', 'value'=>'', 'type'=>'select'), array( 'list'=>array($this->lng['txtAlege']) + $opGestiuni, 'label'=>'Gestiunea destinatie', 'columns'=>'col-xs-12 col-md-6' ) );

            $users = $modelGestiuni->getUsers($row->id_gestiune_sursa);
            $fe['id_resp_sursa'] = new FormElement( array( 'id'=>'fe_id_resp_sursa', 'name'=>'id_resp_sursa', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array( 'list'=>$users, 'label'=>$this->lng['txtResponsabilGestiuneSursa'], 'columns'=>'col-xs-12 col-md-6' ) );
            $users = $modelGestiuni->getUsers($row->id_gestiune);
            $fe['id_resp'] = new FormElement( array( 'id'=>'fe_id_resp', 'name'=>'id_resp', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array( 'list'=>$users, 'label'=>$this->lng['txtResponsabilGestiuneDestinatie'], 'columns'=>'col-xs-12 col-md-6' ) );
            $fe['id_serie'] = new FormElement( array('name'=>'id_serie', 'value'=>'', 'type'=>'select'), array( 'list'=>$opSerii, 'label'=>$this->lng['txtSerieNr'], 'columns'=>'col-xs-12' ) );

            foreach ($fe as $key=>$formElement) { $formElement->value = $row->$key; }

        } else {
            $fe['id_gestiune_sursa'] = new FormElement( array( 'id'=>'id_gestiune_sursa', 'name'=>'id_gestiune_sursa', 'data-action'=>'ajax-update', 'value'=>'', 'type'=>'static'), array( 'label'=>$this->lng['txtGestiuneaSursa'], 'columns'=>'col-xs-12 col-md-6' ) );
            $fe['id_gestiune'] = new FormElement( array( 'id'=>'id_gestiune', 'name'=>'id_gestiune', 'data-action'=>'ajax-update', 'value'=>'', 'type'=>'static'), array( 'list'=>array($this->lng['txtAlege']) + $opGestiuni, 'label'=>$this->lng['txtGestiuneaDestinatie'], 'columns'=>'col-xs-12 col-md-6' ) );

            $fe['id_resp_sursa'] = new FormElement( array( 'id'=>'fe_id_resp_sursa', 'name'=>'id_resp_sursa', 'value'=>'', 'type'=>'static'), array('label'=>$this->lng['txtResponsabilGestiuneSursa'], 'columns'=>'col-xs-12 col-md-6' ) );
            $fe['id_resp'] = new FormElement( array( 'id'=>'fe_id_resp', 'name'=>'id_resp', 'value'=>'', 'type'=>'static'), array('label'=>$this->lng['txtResponsabilGestiuneDestinatie'], 'columns'=>'col-xs-12 col-md-6' ) );

            $fe['id_serie'] = new FormElement( array('name'=>'id_serie', 'value'=>'', 'type'=>'select'), array( 'list'=>$opSerii, 'label'=>$this->lng['txtSerieNr'], 'columns'=>'col-xs-12' ) );

            foreach ($fe as $key=>$formElement) { $formElement->value = $row->$key; }
            $fe['id_gestiune_sursa']->value = $opGestiuni[$row->id_gestiune_sursa];
            $fe['id_gestiune']->value = $opGestiuni[$row->id_gestiune];
            $users = $modelGestiuni->getUsers($row->id_gestiune_sursa);
            $fe['id_resp_sursa']->value = $users[$row->id_resp_sursa];
            $users = $modelGestiuni->getUsers($row->id_gestiune);
            $fe['id_resp']->value = $users[$row->id_resp];
        }

        $fe['reload'] = new FormElement( array('name'=>'reload', 'type'=>'hidden', 'class'=>'', 'value'=>isset($_GET['reload']) && $_GET['reload']==1 ? 1 : 0) );
        $fe['tk'] = new FormElement( array('name'=>'tk', 'type'=>'hidden', 'class'=>'', 'value'=>scramble($this->authUser->id.'edit_FT'.$row->id)) );
        $fe['id'] = new FormElement( array('name'=>'id', 'type'=>'hidden', 'class'=>'', 'value'=>$row->id) );

        $vars = array(
            'fe' => $fe,
            'actionMethod' => $_GET['action'],
            'modalTitle' => '<i class="fa fa-pencil"></i> '.$this->lng['txtFisaDeTransfer'].$row->serie.$this->lng['txtSpaceNrSpace'].$row->nr_char,
            'modalCloseButton' => $this->lng['txtInchide'],
            'modalActionButton' =>$this->lng['txtSalveaza'],
            'modalBody' => $allOk ? '' : '<div class="callout callout-warning"><p>'.$this->lng['txtAtentieAvetiProduseDeFisaDeTransferModulEditEsteRestrictivGestiuneaSiIncadrareaNuPotFiModificate'].'</p></div>',
            'modalAlert' => '',
            'action' => $this->router->url('fisetransfer-action'),
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_form', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_edit() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => '' ); }
        $reload = isset($_POST['reload']) ? (int)$_POST['reload'] : 0;

        $id = isset($_POST['id']) && isset($_POST['tk']) && ($_POST['tk'] == scramble($this->authUser->id.'edit_FT'.$_POST['id'])) ? $_POST['id'] : 0;
        if (!$id) { return array( 'error'=>2, 'html'=>'', 'errorMessage'=>$this->lng['txtAAparutOEroareInternaIncercati'], 'js' => '' ); }

        $modelFT = new GestiuneActe($this->registry); $row = $modelFT->getOne( array( 'id' => $id, 'type'=>$type, 'gestiune_sursa'=>1 ) );
        if (!$row) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => '' ); }

        $modelEntries = new GestiuneTranzactii( $this->registry );
        $entries = $modelEntries->count( array( 'id_gestiune_act' => $row->id) );
        if($entries > 0) {return  array( 'error'=>2, 'html'=>'', 'errorMessage'=>$this->lng['txtEroriAvetiProduseDeFisaDeTransferModulEditEsteRestrictiv'], 'js' => '', 'error_list' => array());}


        $model = new Partners($this->registry);
        $opFirme = $model->getArray(array('type' => 4));

        $modelGestiuni = new Gestiuni($this->registry);
        $opGestiuni = $modelGestiuni->getArray(array('id_firma'=>array_keys($opFirme)));

        $model = new SeriiDocumente($this->registry);
        $seria = $model->getOne( array( 'id'=>$_POST['id_serie'], 'id_furnizor'=>array_keys($opFirme), 'type'=>$type+10 ) );

        $fe = array();
        $fe['id_gestiune_sursa'] = new FormElement( array( 'id'=>'id_gestiune_sursa', 'name'=>'id_gestiune_sursa', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array('list'=>array($this->lng['txtAlege']) + $opGestiuni) );
        $fe['id_gestiune'] = new FormElement( array( 'id'=>'id_gestiune', 'name'=>'id_gestiune', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array('list'=>array($this->lng['txtAlege']) + $opGestiuni) );

        $model = new Gestiuni($this->registry);
        $opUsers = $model->getUsers($_POST['id_gestiune_sursa']);
        $fe['id_resp_sursa'] = new FormElement( array( 'id'=>'fe_id_resp_sursa', 'name'=>'id_resp_sursa', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array( 'list'=>$opUsers) );
        $opUsers = $model->getUsers($_POST['id_gestiune']);
        $fe['id_resp'] = new FormElement( array( 'id'=>'fe_id_resp', 'name'=>'id_resp', 'class'=>'form-control s2', 'value'=>'', 'type'=>'select'), array( 'list'=>$opUsers));

        $errors= array();
        if($_POST['id_gestiune_sursa'] == $_POST['id_gestiune']){$errors[] = 'id_gestiune_sursa'; $errors[] = 'id_gestiune'; return array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriAtiAlesAceleasiGestiuni'].' </p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',', $errors) );}

        foreach ($fe as $formElement) { if(!$formElement->get()) { $errors[] = $formElement->name; } }

        if (!$errors) {
            foreach ($fe as $key=>$formElement) { $row->$key = $formElement->value; }
            $modelFT->save($row);
            $message = $this->lng['txtModificarileAuFostSalvateSucces'];
            if ($reload) { $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') ); }
            $result = array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => $reload ? 'window.location.reload()' : '' );
        } else {
            $result = array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriVaRugamCompletatiCampurileEvidentiate'].'</p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',', $errors) );
        }
        return $result;
    }

    /**
     * ADAUGA PRODUS
    */
    private function modal_produse_adauga() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => '' ); }
        $idFT = isset($_GET['id_parent']) ? $_GET['id_parent'] : 0;
        $model = new GestiuneActe($this->registry); $row = $model->getOne( array( 'id' => $idFT, 'type'=>$type, 'gestiune_sursa'=>1 ) );

        if (!$row) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => '' ); }

            $op = array();
            $op['id_gestiune'] = $row->id_gestiune_sursa;
            $op['pe_stoc'] = 1;

        $model = new GestiuneTranzactii($this->registry); $opProduse = $model->getArrayStoc($op);

        $fe = array();
        $fe['id_nomenclator'] = new FormElement( array( 'name'=>'id_nomenclator', 'class'=>'form-control s2', 'style'=>'width:100%', 'value'=>'0', 'type'=>'select'), array( 'label'=>$this->lng['txtAlegetiProdusulDinGestiunea'].' '.$row->gestiune_sursa, 'list'=>array($this->lng['txtAlegeUnProdus']) + $opProduse, 'columns'=>'col-xs-12 col-md-10' ) );
        $fe['buc'] = new FormElement( array( 'name'=>'buc', 'value'=>1, 'type'=>'number'), array( 'label'=>$this->lng['txtCantitate'], 'columns'=>'col-xs-12 col-md-2' ) );

        $fe['id'] = new FormElement( array('name'=>'id', 'type'=>'hidden', 'class'=>'', 'value'=>$idFT) );
        $fe['id_gestiune'] = new FormElement( array('name'=>'id_gestiune', 'type'=>'hidden', 'class'=>'', 'value'=>$row->id_gestiune_sursa) );

        $fe['reload'] = new FormElement( array('name'=>'reload', 'type'=>'hidden', 'class'=>'', 'value'=>isset($_GET['reload']) && $_GET['reload']==1 ? 1 : 0) );
        $fe['tk'] = new FormElement( array('name'=>'tk', 'type'=>'hidden', 'class'=>'', 'value'=>scramble($this->authUser->id.'addpft'.$idFT)) );

        $vars = array(
            'fe' => $fe,
            'actionMethod' => $_GET['action'],
            'modalTitle' => '<i class="fa fa-plus"></i> '.$this->lng['txtAdaugaUnProdusPeFisaDeTransfer'],
            'modalCloseButton' => $this->lng['txtInchide'],
            'modalActionButton' => $this->lng['txtAdauga'],
            'modalBody' => '',
            'modalAlert' => '',
            'bc' => $row,
            'action' => $this->router->url('fisetransfer-action'),
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_add_item_ft', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_produse_adauga() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => '' ); }

        $idFT = isset($_POST['id']) && isset($_POST['tk']) && ($_POST['tk'] == scramble($this->authUser->id.'addpft'.$_POST['id'])) ? $_POST['id'] : 0;
        if (!$idFT) { return array( 'error'=>2, 'html'=>'', 'errorMessage'=>$this->lng['txtAAparutOEroareInternaIncercati'], 'js' => '' ); }

        $modelFT = new GestiuneActe($this->registry); $ft = $modelFT->getOne( array( 'id' => $idFT, 'type'=>$type) );
        if (!$ft) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => '' ); }

        $reload = isset($_POST['reload']) ? (int)$_POST['reload'] : 0;

        $model = new GestiuneTranzactii($this->registry); $opProduse = $model->getArrayStoc(array('id_gestiune' => $ft->id_gestiune_sursa, 'pe_stoc'=>1));

        $fe = array();

        $fe['id_nomenclator'] = new FormElement( array( 'name'=>'id_nomenclator', 'type'=>'text'), array( 'required'=>1, 'list'=>$opProduse ) );
        $fe['buc'] = new FormElement( array( 'name'=>'buc', 'type'=>'number'), array( 'required'=>1 ) );

        $errors = array();
        foreach ($fe as $formElement) { if(!$formElement->get()) { $errors[] = $formElement->name; } }

        if (!$errors) {
            $rows = $model->get( array( 'pe_stoc'=>1, 'type'=>array(1,3,5), 'id_gestiune'=>$ft->id_gestiune_sursa, 'id_nomenclator'=>$fe['id_nomenclator']->value ) );
            if (!$rows) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtProduseleNuSeMaiGasescInGestiune'], 'html'=>'', 'js' => '' ); }
            $stoc = suma($rows, 'stoc');
            if ($fe['buc']->value > $stoc){ return array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p> '.$this->lng['txtEroriNuExista'].$fe['buc']->value.' '.$this->lng['txtProdusePeStoc'].'</p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => $fe['buc']->name );}

            foreach($rows as $row) {
                if (isset($_POST['cant-'.$row->id]) && is_numeric($_POST['cant-'.$row->id]) && $_POST['cant-'.$row->id]<=$row->stoc) {
                    $buc = $_POST['cant-'.$row->id];
                    if ($buc != 0) {
                        $row->stoc = $row->stoc - $buc;
                        $model->save($row);
                        $row->id_parent = $row->id;
                        $row->id = 0;
                        $row->id_gestiune_act = $ft->id;
                        $row->stoc = 0;
                        $row->type = $type;
                        $row->buc = -1 * $buc;
                        $row->ignore = 1;
                        $model->save($row);
                        $row->id_parent = $row->id;
                        $row->id = 0;
                        $row->id_gestiune_act = $ft->id;
                        $row->stoc = 0;
                        $row->buc_blocate = $buc;
                        $row->type = $type;
                        $row->buc = $buc;
                        $row->ignore = 0;
                        $model->save($row);
                    }
                } else {
                    return array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriCantitateaDeVanzareDepasesteStoculDisponibil'].'</p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',', array('cant-'.$row->id)) );
                }
            }

            $modelFT->updateValori($ft->id);

            $message = $this->lng['txtProdusulAFostAdaugatPeFisaDeTransfer'];
            if ($reload) { $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') ); }
            $result = array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => $reload ? 'window.location.reload()' : '');
        } else {
            $result = array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriVaRugamCompletatiCampurileEvidentiate'].'</p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',', $errors) );
        }
        return $result;
    }

    /**
     * ADAUGA PRODUSE BULK
    */
    private function modal_produse_bulk_adauga() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => '' ); }
        $idFT = isset($_GET['id_parent']) ? $_GET['id_parent'] : 0;
        $model = new GestiuneActe($this->registry); $row = $model->getOne( array( 'id' => $idFT, 'type'=>$type, 'gestiune_sursa'=>1 ) );

        if (!$row) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => '' );}

        $fe = array();

        $fe['id_gestiune_act'] = new FormElement( array('name'=>'id_gestiune_act', 'type'=>'hidden', 'class'=>'', 'value'=>$idFT) );
        $fe['id_gestiune'] = new FormElement( array('name'=>'id_gestiune', 'type'=>'hidden', 'class'=>'', 'value'=>$row->id_gestiune_sursa) );

        $fe['reload'] = new FormElement( array('name'=>'reload', 'type'=>'hidden', 'class'=>'', 'value'=>isset($_GET['reload']) && $_GET['reload']==1 ? 1 : 0) );
        $fe['tk'] = new FormElement( array('name'=>'tk', 'type'=>'hidden', 'class'=>'', 'value'=>scramble($this->authUser->id.'addpft'.$idFT)) );

        $vars = array(
            'fe' => $fe,
            'actionMethod' => $_GET['action'],
            'modalTitle' => '<i class="fa fa-plus"></i> '.$this->lng['txtAdaugaProdusePeBonulDeMarfa'],
            'modalCloseButton' => $this->lng['txtInchide'],
            'modalActionButton' => $this->lng['txtAdauga'],
            'modalBody' => '',
            'modalAlert' => '',
            'bc' => $row,
            'action' => $this->router->url('fisetransfer-action'),
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_add_item_bulk', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_produse_bulk_adauga() {
        $type = 5;
        $errors = array();
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => '' ); }

        $idFT = isset($_POST['id_gestiune_act']) && isset($_POST['tk']) && ($_POST['tk'] == scramble($this->authUser->id.'addpft'.$_POST['id_gestiune_act'])) ? $_POST['id_gestiune_act'] : 0;
        if (!$idFT) { return array( 'error'=>2, 'html'=>'', 'errorMessage'=>$this->lng['txtAAparutOEroareInternaIncercati'], 'js' => '' ); }

        $modelFT = new GestiuneActe($this->registry); $ft = $modelFT->getOne( array( 'id' => $idFT, 'type'=>$type) );
        if (!$ft) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => '' ); }

        $reload = isset($_POST['reload']) ? (int)$_POST['reload'] : 0;

        $model = new GestiuneTranzactii($this->registry);
        $rows = $model->getArraycompact(array('id_gestiune' => $ft->id_gestiune_sursa, 'pe_stoc'=>1));

        foreach($rows as $row){
            if (isset($_POST['cant-'.$row->id]) && is_numeric($_POST['cant-'.$row->id]) && $_POST['cant-'.$row->id] && $_POST['cant-'.$row->id]>$row->stoc){
                $errors[] = 'cant-'.$row->id;
            }
        }
        if (!$errors) {
            foreach($rows as $row) {
                if (isset($_POST['cant-'.$row->id]) && is_numeric($_POST['cant-'.$row->id]) && $_POST['cant-'.$row->id]<=$row->stoc) {
                    $buc = $_POST['cant-'.$row->id];
                    $produse = $model->get(array('id_nomenclator'=>$row->id, 'id_gestiune'=>$_POST['id_gestiune'], 'type'=>array(1,3,5)));
                    if ($buc != 0) {
                        $row->stoc = $row->stoc - $buc;
                        $model->save($row);
                        $row->id_parent = $row->id;
                        $row->id = 0;
                        $row->id_gestiune_act = $ft->id;
                        $row->stoc = 0;
                        $row->type = $type;
                        $row->buc = -1 * $buc;
                        $row->ignore = 1;
                        $model->save($row);
                        $row->id_parent = $row->id;
                        $row->id = 0;
                        $row->id_gestiune_act = $ft->id;
                        $row->stoc = $buc;
                        $row->type = $type;
                        $row->buc = $buc;
                        $row->ignore = 0;
                        $model->save($row);
                    }
                }
            }

            $modelFT->updateValori($ft->id);

            $message = $this->lng['txtProdusulAFostAdaugatPeFisaDeTransfer'];
            if ($reload) { $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') ); }
            $result = array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => $reload ? 'window.location.reload()' : '');
        } else {
            $result = array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriCantitateaDeVanzareDepasesteStoculDisponibil'].'</p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',' ,$errors));
        }
        return $result;
    }

    /**
     * STERGE PRODUS
    */
    private function modal_produs_sterge() {
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => '' ); }
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $idFT = isset($_GET['id_parent']) ? $_GET['id_parent'] : 0;

        $model = new GestiuneTranzactii($this->registry); $row = $model->getOne( array( 'id' => $id , 'id_gestiune_act'=>$idFT) );

        if (!$row) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtProdusulSolicitatNuExista'], 'html'=>'', 'js' => '' ); }

        $vars = array(
            'confirmTxt' => $this->lng['txtProdusul'].' <b>'.$row->title.'</b> '.$this->lng['txtVaFiStersDePeFisaDeTransferEstiSigur'],
            'url' => $this->router->url('fisetransfer-action/'),
            'actionMethod' => $_GET['action'],
            'id' => $id,
            'tk' => scramble($this->authUser->id.'del_ftli'.$row->id.$row->id_gestiune_act),
        );
        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_sterge', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_produs_sterge() {
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuPotiStergeFiseDeTransfer'], 'html'=>'', 'js' => '' ); }

        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $model = new GestiuneTranzactii($this->registry); $row = $model->getOne( array( 'id' => $id ) );

        if (!$row || !isset($_GET['tk']) || $_GET['tk'] != scramble($this->authUser->id.'del_ftli'.$row->id.$row->id_gestiune_act)) {
            return array( 'error'=>1, 'html'=>'', 'errorMessage'=>$this->lng['txtAAparutOEroareInternaIncercati'], 'js' => '' );
        }

        if ($row->group_id && !isset($_GET['ignore_group'])) {
            $rows = $model->get( array( 'group_id' => $row->group_id, 'id_gestiune_act'=>$row->id_gestiune_act, 'sort'=>'t1.`id` DESC' ) );
            foreach($rows as $row) { if (isset($stoc)){$row->stoc = $stoc;} $model->delete($row); $stoc = $row->stoc; }
        } else {
            $model->delete($row);
            $desters = $model->getOne(array('id'=>$row->id_parent));
            $model->delete($desters);
        }

        $message = $this->lng['txtProdusulAFostStersCuSucces'];
        $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=> 'success') );
        return array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'id'=>$id, 'js' => 'window.location.reload()' );
    }

    /**
     * VEZI SERII
    */
    private function modal_produs_view() {
        $type = 5;

        $idFT = isset($_GET['id_parent']) ? $_GET['id_parent'] : 0;
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $idGroup = isset($_GET['group_id']) ? $_GET['group_id'] : 0;

        $modelNir = new GestiuneActe($this->registry);
        $modelItems = new GestiuneTranzactii($this->registry);

        $items = $idGroup ? $modelItems->get(array( 'group_id' => $idGroup, 'id_gestiune_act'=>$idFT, 'ignore'=>0 )) : ($id ? $modelItems->get(array( 'id' => $id, 'id_gestiune_act'=>$idFT, 'ignore'=>0 )) : array() );

        if (!$items) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtRepereleSolicitateNuAuFostGasite'], 'html'=>'', 'js' => '' ); }

        $vars = array(
            'modalTitle' => '<i class="fa fa-file-o"></i> '.$items[0]->title,
            'modalCloseButton' => $this->lng['txtInchide'],
            'modalBody' => '',
            'modalAlert' => '',
            'items' => $items
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/modal_serii_view', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    /**
     * VEZI SERII
    */
    private function modal_produse_view() {
        $type = 5;

        $idFT = isset($_GET['id']) ? $_GET['id'] : 0;

        $modelNir = new GestiuneActe($this->registry);
        $modelItems = new GestiuneTranzactii($this->registry);

        $items = $modelItems->get(array('id_gestiune_act'=>$idFT, 'ignore'=>0 ));

        if (!$items) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtRepereleSolicitateNuAuFostGasite'], 'html'=>'', 'js' => '' ); }

        $vars = array(
            'modalTitle' => '<i class="fa fa-file-o"></i> '.$this->lng['txtProdusePeFisaDeTransfer'],
            'modalCloseButton' => $this->lng['txtInchide'],
            'modalBody' => '',
            'modalAlert' => '',
            'items' => $items
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/modal_serii_view', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    /**
     * UPDATE SERII GENEREAZA
    */
    private function modal_serii_genereaza() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array('error'=>1, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => ''); }

        $idFT = isset($_GET['id_parent']) ? $_GET['id_parent'] : 0;
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $idGroup = isset($_GET['group_id']) ? $_GET['group_id'] : 0;

        $modelFT = new GestiuneActe($this->registry);
        $modelEntries = new GestiuneTranzactii($this->registry);

        $row = $modelFT->getOne(array( 'type'=>$type, 'id'=>$idFT));
        if (!$row) { return array('error'=>1, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => ''); }

        $rows = $idGroup ? $modelEntries->get(array('id_gestiune_act'=>$idFT, 'group_id' => $idGroup, 'ignore'=>0)) : $modelEntries->get(array('id_gestiune_act' => $idFT, 'id' => $id, 'ignore'=>0));

        if (!$idGroup && $rows[0]->group_id) {
            $idGroup = $rows[0]->group_id;
            $rows = $modelEntries->get(array('id_gestiune_act'=>$idFT, 'group_id' => $idGroup, 'ignore'=>0));
        }

        if (!$rows) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtReperulSolicitatNuExista'], 'html'=>'', 'js' => '' ); }

        $vars = array(
            'url' => $this->router->url('fisetransfer-action/'),
            'confirmTxt' => $this->lng['txtSistemulVaGeneraAutomatSeriiUnicePentruToateCele'].' <b>'.( $idGroup ? count($rows) : $rows[0]->buc).$this->lng['txtProduseMic'].' </b>. '.$this->lng['txtDacaExistaSeriiExistenteAcesteaSeVorInlocui'],
            'confirmTitle' => 'Generare serii',
            'data' => array('action' => $_GET['action'], 'id_parent'=>$idFT, 'id'=>$id, 'group_id'=>$idGroup, 'tk'=>scramble( $idFT.$id.$idGroup.$this->authUser->id)),
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_confirm', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_serii_genereaza() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array('error'=>1, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => ''); }

        $idFT = isset($_GET['id_parent']) ? $_GET['id_parent'] : 0;
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $idGroup = isset($_GET['group_id']) ? $_GET['group_id'] : 0;

        $reload = 1;

        $tk = isset($_GET['tk']) ? $_GET['tk'] : '';
        if (!$tk || $tk!=scramble($idFT.$id.$idGroup.$this->authUser->id)) { return array('error'=>1, 'errorMessage'=>$this->lng['txtTokenInvalid'], 'html'=>'', 'js' => ''); }

        $modelFT = new GestiuneActe($this->registry);
        $modelEntries = new GestiuneTranzactii($this->registry);

        $row = $modelFT->getOne(array( 'type'=>$type, 'id'=>$idFT));
        if (!$row) { return array('error'=>1, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => ''); }

        $rows = $idGroup ? $modelEntries->get(array('id_gestiune_act'=>$idFT, 'group_id' => $idGroup, 'ignore'=>0)) : $modelEntries->get(array('id_gestiune_act' => $idFT, 'id' => $id, 'ignore'=>0));

        if (!$idGroup && $rows[0]->group_id) {
            $idGroup = $rows[0]->group_id;
            $rows = $modelEntries->get(array('id_gestiune_act'=>$idFT, 'group_id' => $idGroup, 'ignore'=>0));
        }

        if (!$rows) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtReperulSolicitatNuExista'], 'html'=>'', 'js' => '' ); }

        require_once 'ean13/ean_parity.php';

        if ($idGroup) {
            $i=0;
            foreach($rows as $r) {
                if ($r->buc == $r->stoc) {
                    $i++;
                    $uniqueId = substr(str_pad($r->id.$i.rand(0, 9999), 12, '0', STR_PAD_RIGHT), 0, 12);
                    $r->serie = $uniqueId.ean_checksum($uniqueId);
                    $modelEntries->save($r);
                }
            }
        } else {
            $idGroup = $modelEntries->explode($rows[0]);
            $rows = $modelEntries->get(array('group_id' => $idGroup, 'id_gestiune_act'=>$idFT, 'ignore'=>0));
            foreach ($rows as $key=>$r) {
                $uniqueId = substr(str_pad($r->id.($key+1).rand(0, 9999), 12, '0', STR_PAD_RIGHT), 0, 12);
                $r->serie = $uniqueId.ean_checksum($uniqueId);
                $modelEntries->save($r);
            }
        }

        $message = $this->lng['txtSeriileSauGeneratCuSucce'];
        $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') );
        return array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => $reload ? 'window.location.reload()' : '' );
    }

    /**
     * UPDATE SERII MANUAL
    */
    private function modal_serii_edit() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array('error'=>1, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => ''); }

        $idFT = isset($_GET['id_parent']) ? $_GET['id_parent'] : 0;
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $idGroup = isset($_GET['group_id']) ? $_GET['group_id'] : 0;

        $modelFT = new GestiuneActe($this->registry);
        $modelEntries = new GestiuneTranzactii($this->registry);

        $row = $modelFT->getOne(array( 'type'=>$type, 'id'=>$idFT,));
        if (!$row) { return array('error'=>1, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => ''); }

        $rows = $idGroup ? $modelEntries->get(array('id_gestiune_act'=>$idFT, 'group_id' => $idGroup, 'ignore'=>0)) : $modelEntries->get(array('id_gestiune_act' => $idFT, 'id' => $id, 'ignore'=>0));

        if (!$idGroup && $rows[0]->group_id) {
            $idGroup = $rows[0]->group_id;
            $rows = $modelEntries->get(array('id_gestiune_act'=>$idFT, 'group_id' => $idGroup));
        }

        if (!$rows) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtReperulSolicitatNuExista'], 'html'=>'', 'js' => '' ); }

        $fei = array();
        if ($idGroup) {
            foreach($rows as $r) {
                $fei['serie'.$r->id] = new FormElement( array('name'=>'serie'.$r->id, 'value'=>$r->serie, 'type'=>'text'), array( 'label'=>$this->lng['txtSerie'], 'label_visible'=>false, 'columns'=>'' ) );
                $fei['sarja'.$r->id] = new FormElement( array('name'=>'sarja'.$r->id, 'value'=>$r->sarja, 'type'=>'text'), array( 'label'=>$this->lng['txtSarja'], 'label_visible'=>false, 'columns'=>'' ) );
                $fei['lot'.$r->id] = new FormElement( array('name'=>'lot'.$r->id, 'value'=>$r->lot, 'type'=>'text'), array( 'label'=>'Lot', 'label_visible'=>false, 'columns'=>'' ) );
            }
        } else {
            for($i=0; $i<$rows[0]->buc; $i++) {
                $fei['serie'.$i] = new FormElement( array('name'=>'serie'.$i, 'value'=>$rows[0]->serie, 'type'=>'text'), array( 'label'=>$this->lng['txtSerie'], 'label_visible'=>false, 'columns'=>'' ) );
                $fei['sarja'.$i] = new FormElement( array('name'=>'sarja'.$i, 'value'=>$rows[0]->sarja, 'type'=>'text'), array( 'label'=>$this->lng['txtSarja'], 'label_visible'=>false, 'columns'=>'' ) );
                $fei['lot'.$i] = new FormElement( array('name'=>'lot'.$i, 'value'=>$rows[0]->lot, 'type'=>'text'), array( 'label'=>'Lot', 'label_visible'=>false, 'columns'=>'' ) );
            }
        }

        $fe = array();
        $fe['id_parent'] = new FormElement( array('name'=>'id_parent', 'type'=>'hidden', 'class'=>'', 'value'=>$idFT) );
        $fe['id'] = new FormElement( array('name'=>'id', 'type'=>'hidden', 'class'=>'', 'value'=>$id) );
        $fe['group_id'] = new FormElement( array('name'=>'group_id', 'type'=>'hidden', 'class'=>'', 'value'=>$idGroup) );

        $fe['reload'] = new FormElement( array('name'=>'reload', 'type'=>'hidden', 'class'=>'', 'value'=>isset($_GET['reload']) && $_GET['reload']==1 ? 1 : 0) );
        $fe['tk'] = new FormElement( array('name'=>'tk', 'type'=>'hidden', 'class'=>'', 'value'=>scramble($this->authUser->id.'nirupdate'.$row->id)) );

        $vars = array(
            'fe' => $fe,
            'actionMethod' => $_GET['action'],
            'modalTitle' => '<i class="fa fa-barcode"></i> '.$rows[0]->title,
            'modalCloseButton' => $this->lng['txtInchide'],
            'modalActionButton' => $this->lng['txtActualizeaza'],
            'modalBody' => '',
            'modalAlert' => '',
            'fei' => $fei,
            'largeModal' => 0,
            'idGroup' => $idGroup,
            'rows' => $rows,
            'action' => $this->router->url('fisetransfer-action'),
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_nir_serii_manual', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_serii_edit() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtNuPotiEditaFiseDeTransfer'], 'html'=>'', 'js' => '' ); }

        //print_r($_POST);die();

        $idFT = isset($_POST['id_parent']) ? $_POST['id_parent'] : 0;
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $idGroup = isset($_POST['group_id']) ? $_POST['group_id'] : 0;

        $reload = isset($_POST['reload']) ? (int)$_POST['reload'] : 0;

        $modelFT = new GestiuneActe($this->registry);
        $modelEntries = new GestiuneTranzactii($this->registry);

        $row = $modelFT->getOne(array( 'type'=>$type, 'id'=>$idFT ));
        if (!$row) { return array('error'=>2, 'errorMessage'=>$this->lng['txtFisaDeTransferSolicitataNuExista'], 'html'=>'', 'js' => ''); }

        $rows = $idGroup ? $modelEntries->get(array('id_gestiune_act'=>$idFT, 'group_id' => $idGroup, 'ignore'=>0)) : $modelEntries->get(array('id_gestiune_act' => $idFT, 'id' => $id, 'ignore'=>0));
        if (!$rows) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtReperulSolicitatNuExista'], 'html'=>'', 'js' => '' ); }

        if (!isset($_POST['tk']) || $_POST['tk']!=scramble($this->authUser->id.'nirupdate'.$row->id)) { return array('error'=>2, 'errorMessage'=>$this->lng['txtTokenInvalid'], 'html'=>'', 'js' => ''); }

        $fei = array();
        if ($idGroup) {
            foreach($rows as $r) {
                $fei['serie'.$r->id] = new FormElement( array('name'=>'serie'.$r->id, 'type'=>'text'), array( ) );
                $fei['sarja'.$r->id] = new FormElement( array('name'=>'sarja'.$r->id, 'type'=>'text'), array( ) );
                $fei['lot'.$r->id] = new FormElement( array('name'=>'lot'.$r->id, 'type'=>'text'), array( ) );
            }
        } else {
            for($i=0; $i<$rows[0]->buc; $i++) {
                $fei['serie'.$i] = new FormElement( array('name'=>'serie'.$i, 'type'=>'text'), array( ) );
                $fei['sarja'.$i] = new FormElement( array('name'=>'sarja'.$i, 'type'=>'text'), array( ) );
                $fei['lot'.$i] = new FormElement( array('name'=>'lot'.$i, 'type'=>'text'), array( ) );
            }
        }

        $errors = array();
        foreach ($fei as $formElement) { if(!$formElement->get()) { $errors[] = $formElement->name; } }

        if (!$errors) {
            if ($idGroup) {
                foreach($rows as $r) {
                    if ($r->buc == $r->stoc) {
                        $r->serie = $fei['serie'.$r->id]->value;
                        $r->sarja = $fei['sarja'.$r->id]->value;
                        $r->lot = $fei['lot'.$r->id]->value;
                        $modelEntries->save($r);
                    }
                }
            } else {
                $idGroup = $modelEntries->explode($rows[0]);
                $rows = $modelEntries->get(array('group_id' => $idGroup, 'id_gestiune_act'=>$idNT, 'ignore'=>0));
                foreach ($rows as $key=>$r) {
                    $r->serie = $fei['serie'.$key]->value;
                    $r->sarja = $fei['sarja'.$key]->value;
                    $r->lot = $fei['lot'.$key]->value;
                    $modelEntries->save($r);
                }
            }

            if (!$errors) {
                $message = $this->lng['txtSeriileAuFostActualizateCuSucces'];
                if ($reload) { $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') ); }
                $result = array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => $reload ? 'window.location.reload()' : '' );
            } else {
                $result = array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriNuToateCantitatileSauActualizatStocInsuficientPentruCeleEvidentiate'].'</p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',', $errors) );
            }
        } else {
            $result = array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroriVaRugamCompletatiCampurileEvidentiate'].'</p></div>', 'errorMessage'=>'', 'js' => '', 'error_list' => implode(',', $errors) );
        }
        return $result;
    }

    /**
     * IMPORT SERII XLSX
    */
    private function modal_serii_import() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuAiPermisiuneasaModificiNoteleDeReceptie'], 'html'=>'', 'js' => '' ); }

        $idFT = isset($_GET['id_parent']) ? $_GET['id_parent'] : 0;
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $idGroup = isset($_GET['group_id']) ? $_GET['group_id'] : 0;

        $modelFT = new GestiuneActe($this->registry);
        $modelItems = new GestiuneTranzactii($this->registry);

        $object = $id ? $modelItems->getOne( array( 'id' => $id, 'id_gestiune_act'=>$idFT, 'ignore'=>0 ) ) : false;

        if (!$object) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtReperulSolicitatNuAFostGasit'], 'html'=>'', 'js' => '' ); }

        $vars = array(
            'body' => $this->lng['txtImportaSeriileFolosindUnFisierExcel'],
            'url' => $this->router->url('fisetransfer-action/'),
            'data' => array( 'action' =>$_GET['action'], 'id_parent'=>$idFT, 'id'=>$id, 'group_id'=>$idGroup, 'tk'=>scramble($this->authUser->id.$id.$idFT.'nirseriixls') ),
            'example_file' => 'xlsx_examples/serii_import.xlsx',
        );
        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_import', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_serii_import() {
        $type = 5;
        if (!$this->user_can('fisetransfer/edit')) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtNuAiPermisiuneaSaImportiPrduseSiServicii'], 'html'=>'', 'js' => '' ); }

        $idFT = isset($_POST['id_parent']) ? $_POST['id_parent'] : 0;
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $idGroup = isset($_POST['group_id']) ? $_POST['group_id'] : 0;

        if (!isset($_FILES['fisier_xlsx']) || $_FILES['fisier_xlsx']['error']) { return array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroareLaTransferFisierIncercatiDinNou'].'</p></div>', 'errorMessage'=>'', 'js' => '' ); }

        if (!$idFT || !$id || !isset($_POST['tk']) || $_POST['tk'] != scramble($this->authUser->id.$id.$idFT.'nirseriixls')) { return array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtAAparutOEroareInternaIncercati'].'</p></div>', 'errorMessage'=>'', 'js' => '' ); }

        $modelFT = new GestiuneActe($this->registry);
        $modelItems = new GestiuneTranzactii($this->registry);

        $ft = $modelFT->getOne( array( 'id'=>$idFT, 'type'=>$type) );

        if (!$ft) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtEroareInterna'], 'html'=>'', 'js' => '' ); }

        if ($idGroup) {
            $items = $modelItems->get(array( 'group_id' => $idGroup, 'id_gestiune_act'=>$idFT, 'ignore'=>0));
        } else {
            $item = $modelItems->getOne(array( 'id' => $id, 'id_gestiune_act'=>$idFT, 'ignore'=>0));
            if ($item) {
                if ($item->group_id) {
                    $items = $modelItems->get(array( 'group_id' => $item->group_id, 'id_gestiune_act'=>$idFT));
                } else {
                    $idGroup = $modelItems->explode($item);
                    $items = $modelItems->get(array( 'group_id' => $idGroup, 'id_gestiune_act'=>$idFT));
                }
            } else { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtNuAmGasitRepereleSolicitatePeNIR'], 'html'=>'', 'js' => '' ); }
        }

        //print_r($items);die();

        if (!$items) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtEroareInterna'], 'html'=>'', 'js' => '' ); }

        $modelExcel = new DTExcel($this->registry);

        $allRows = array();
        if (in_array(extension($_FILES['fisier_xlsx']['name']), array('xlsx', 'xls'))) {
            $allRows = $modelExcel->import( $_FILES['fisier_xlsx']['tmp_name'], extension($_FILES['fisier_xlsx']['name']) );
        }
        if (!$allRows) { return array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtEroareLaCitireaFisieruluiIncercatiAlt'].'</p></div>', 'errorMessage'=>'', 'js' => '' ); }

        $allRows = current($allRows); if (!$allRows) { return array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtFisierulNuContineLiniiImport'].'</p></div>', 'errorMessage'=>'', 'js' => '' ); }
        unset($allRows[1]);

        $xlsx = array('serie'=>'B', 'lot'=>'C', 'sarja'=>'D');

        $i = 0;
        foreach ($allRows as $rowNumber => $row) { $row['B'] = trim($row['B']); $row['C'] = trim($row['C']); $row['D'] = trim($row['D']);
            if (!$row['B'] && !$row['C'] && !$row['D']) { continue; }
            if (!isset($items[$i])) { break; }
            foreach($xlsx as $key=>$letter) { $items[$i]->$key = htmlspecialchars($row[$letter]); }
            $modelItems->save($items[$i]);
            $i++;
        }

        if (!$i) { return array( 'error'=>1, 'html'=>'<div class="callout callout-danger"><p>'.$this->lng['txtNuSaImportatNiciOSerieIncercatiAltFisier'].'</p></div>', 'errorMessage'=>'', 'js' => '' ); }

        $message = $this->lng['txtAuFostImportateCuSucces'].' '.$i.' '.$this->lng['txtSeriiMic'];
        $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') );
        return array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => 'window.location.reload()' );
    }

     /**
     * STATUS
    */
    private function modal_status() {
        $type = 5;
        if (!$this->user_can('fisetransfer/status')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuPotiEditaPostari'], 'html'=>'', 'js' => '' ); }

        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        $model = new GestiuneActe($this->registry); $object = $model->getOne( array( 'id' => $id, 'type'=>$type) );

        if (!$object) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtOfertaSolicitataNuExista'], 'html'=>'', 'js' => '' ); }

        $vars = array(
            'url' => $this->router->url('fisetransfer-action/'),
            'confirmTxt' => $this->lng['txtDupaValidareaFiseiDeTransferNiciunCampNuVaMaiFiEditabil'],
            'data' => array('action' => $_GET['action'],'id'=>$id, 'tk'=>scramble($type.$object->id.$this->authUser->id.'offst'), 'reload'=>isset($_GET['reload']) && $_GET['reload']== 1 ? 1 : 0)
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_valideaza', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_status() {
        $type = 5;
        if (!$this->user_can('fisetransfer/status')) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtNuPotiEditaPostari'], 'html'=>'', 'js' => '' ); }

        $model = new GestiuneActe($this->registry);
        $invoices = new Invoices($this->registry);

        $reload = isset($_GET['reload']) ? (int)$_GET['reload'] : 0;
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $object = $id ? $object = $model->getOne( array( 'id' => $id, 'type'=>$type) ) : false;

        if (!$object || !isset($_GET['tk']) || $_GET['tk']!=scramble($type.$object->id.$this->authUser->id.'offst')) { return array( 'error'=>2, 'html'=>'', 'errorMessage'=>$this->lng['txtAAparutOEroareInternaIncercati'], 'js' => '' ); }

        $modelGT = new GestiuneTranzactii($this->registry); $rows = $modelGT->get(array('id_gestiune_act'=>$object->id, 'ignore'=>0));
        $modelGA = new GestiuneActe($this->registry);
        foreach($rows as $row){
            $ignoratul = $modelGT->getOne(array('id'=>$row->id_parent, 'ignore'=>1));
            $ignoratul->id_invoice_entry = 0;
            $modelGT->save($ignoratul);
            $buc = $row->buc_blocate;
            $row->stoc = $row->buc_blocate;
            $row->buc_blocate = 0;
            $idEntry = $row->id_invoice_entry; $row->id_invoice_entry=0;
            $modelGT->save($row);
            $merge = clone $row;
            $row->id_invoice_entry = $idEntry;
            $modelGA->newBonRezervare($object->id_invoice, $row->id_invoice_entry, $row, $buc);
            if ($row->id != $merge->id){
                $merge->stoc = 0;
                $modelGT->save($merge);
            }
        }

        $object->status = 2;
        $model->save($object);
        $message = $this->lng['txtFisaDeTransferAFostValidataCuSucces'];
        if ($reload) { $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') ); }
        $result = array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => $reload ? 'window.location.reload()' : '' );

        return $result;
    }

     /**
     * bulk STATUS
    */
    private function modal_bulk_status() {
        $type = 5;
        if (!$this->user_can('fisetransfer/status')) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtNuPotiEditaPostari'], 'html'=>'', 'js' => '' ); }

        $id = isset($_GET['id_ft']) ? $_GET['id_ft'] : 0;

        $model = new GestiuneActe($this->registry); $object = $model->getOne( array( 'id' => $id, 'type'=>$type) );
        $model = new GestiuneTranzactii($this->registry); $items = $model->get(array( 'id_gestiune_act'=>$object->id, 'ignore'=>0, '!buc_blocate'=>0));
        foreach($items as $item){
            $model = new Nomenclator($this->registry); $stoc = $model->getOne(array('id'=>$item->id_nomenclator));
            if ($stoc){$item->stoc_produs = $stoc->stoc;}
        }

        if (!$object) { return array( 'error'=>1, 'errorMessage'=>$this->lng['txtOfertaSolicitataNuExista'], 'html'=>'', 'js' => '' ); }

        $vars = array(
            'fe'=>array(),
            'items'=>$items,
            'action' => $this->router->url('fisetransfer-action/'),
            'modalTitle' => $this->lng['txtValideaza'].' '.$object->serie.' - '.$object->nr_char.' din '.$object->date,
            'modalAlert' => '',
            'modalBody' => '',
            'modalCloseButton' => $this->lng['txtInchide'],
            'modalActionButton' => '<span class="fa fa-check"> </span> '.$this->lng['txtValideaza'],
            'data' => array('action' => $_GET['action'],'id'=>$id, 'tk'=>scramble($type.$object->id.$this->authUser->id.'valid'), 'reload'=>isset($_GET['reload']) && $_GET['reload']== 1 ? 1 : 0)
        );

        $content = explode('<script>', $this->template->buildFromFile('ajax/fe_modal_bulk_avizeaza', $vars));
        return array( 'error'=>0, 'errorMessage'=>'', 'html'=>$content[0], 'js' => isset($content[1]) ? str_replace('</script>', '', $content[1]) : '' );
    }

    private function action_bulk_status() {
        $type = 5;
        if (!$this->user_can('fisetransfer/status')) { return array( 'error'=>2, 'errorMessage'=>$this->lng['txtNuPotiEditaPostari'], 'html'=>'', 'js' => '' ); }

        $modelGA = new GestiuneActe($this->registry);
        $invoices = new Invoices($this->registry);

        //print_r($_POST);

        $reload = isset($_POST['reload']) ? (int)$_POST['reload'] : 0;
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $object = $id ? $object = $modelGA->getOne( array( 'id' => $id, 'type'=>$type) ) : false;

        if (!$object || !isset($_POST['tk']) || $_POST['tk']!=scramble($type.$object->id.$this->authUser->id.'valid')) { return array( 'error'=>2, 'html'=>'', 'errorMessage'=>$this->lng['txtAAparutOEroareInternaIncercati'], 'js' => '' ); }

        $avizatTotal = true;
        $modelGT = new GestiuneTranzactii($this->registry); $rows = $modelGT->get(array('id_gestiune_act'=>$object->id, 'ignore'=>0));
        foreach($rows as $row){
            if (isset($_POST['cant-'.$row->id]) && $_POST['cant-'.$row->id]){

                $cant = $_POST['cant-'.$row->id];
                $buc = $row->buc_blocate - $cant;
                $idEntry = $object->id_invoice ? $row->id_invoice_entry : 0;
                $ignoratul = $modelGT->getOne(array('id'=>$row->id_parent, 'ignore'=>1));

                if ($buc>0){
                    $row->buc = $buc;
                    $row->buc_blocate = $buc;
                    $row->stoc = 0;
                    $modelGT->save($row);

                    $ignoratul->buc = -1*$buc;
                    $modelGT->save($ignoratul);
                }

                $ignoratul->id_invoice_entry = 0;
                $ignoratul->buc = -1*$cant;
                if ($buc>0){ $ignoratul->id = 0;}
                $modelGT->save($ignoratul);

                $row->stoc = $cant;
                $row->buc = $cant;
                $row->buc_blocate=0;
                $row->id_invoice_entry=0;
                $row->id_parent = $ignoratul->id;
                if ($buc>0){ $row->id = 0;}
                $modelGT->save($row);

                if ($object->id_invoice){
                    $merge = clone $row;
                    $modelGA->newBonRezervare($object->id_invoice, $idEntry, $merge, $cant);
                    if ($row->id != $merge->id){
                        $row->stoc = 0;
                        $modelGT->save($row);
                    }
                }
            }
            if ($row->buc_blocate != 0){ $avizatTotal = false; }
        }

        if($avizatTotal){
            $object->status = 2;
            $modelGA->save($object);
        }
        $message = $this->lng['txtFisaDeTransferAFostValidataCuSucces'];
        if ($reload) { $this->messages->set( array('type'=>'notify', 'message'=>$message, 'className'=>'success') ); }
        $result = array( 'error'=>0, 'html'=>$message, 'errorMessage'=>'', 'js' => $reload ? 'window.location.reload()' : '' );

        return $result;
    }
}
?>
