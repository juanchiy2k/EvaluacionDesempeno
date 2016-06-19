<?php
require('php/fpdf/fpdf.php');


class PDF extends FPDF
{
        private $widths;
	private $aligns;
	
	private $B;
	private $I;
	private $U;
	private $HREF;
        private $lang;
        private $text;

	
	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths=$w;
	}
	
	function SetAligns($a)
	{
		//Set the array of column alignments
		$this->aligns=$a;
	}
	
	function Row($data)
	{
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
		$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=5*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			
			$this->Rect($x,$y,$w,$h);
			
			$this->MultiCell($w,5,$data[$i],0,$a);
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}
	
	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
	}
	
	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
		$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
			$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
					$i++;
				}
				else
				$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
			$i++;
		}
		return $nl;
	}
	
	function Header()
	{
            $this->SetFont('Arial','BI',6);
            $this->Image('images/logo.png', 10, 8,20,15);
            $this->Cell(190,5,  utf8_decode($this->text['date']).': '.$fecha = ($this->lang=='es_ES')?date('d/m/Y').' '.date('h:i:s a'):date('m/d/Y').' '.date('h:i:s a'),0,1,'R');
            $this->RoundedRect(5, 5, 200, 280, 3.50, '1111', '');
	    $this->Ln(20);
	}
	
	function Footer()
	{
            $this->SetY(-20);
            $this->SetFont('Arial','B',6);
            $this->Cell(100,5,utf8_decode($this->text['perf_plan']),0,0,'L');
            $this->Cell(0,5,utf8_decode($this->text['page'].' '.$this->PageNo().'/{nb}'),0,0,'R');
	}
	
	function PDF($orientation='P', $unit='mm', $size='A4')
	{
		// Llama al constructor de la clase padre
		$this->FPDF($orientation,$unit,$size);
		// IniciaciÃ³n de variables
		$this->B = 0;
		$this->I = 0;
		$this->U = 0;
		$this->HREF = '';
                include('internalizacion.php');
                $this->lang = $lang;
                include('locale/textos/text_layout.php');
                $this->text = $textos;
	}
	
	function WriteHTML($html)
	{
		// IntÃ©rprete de HTML
		$html = str_replace("\n",' ',$html);
		$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				// Text
				if($this->HREF)
				$this->PutLink($this->HREF,$e);
				else
				$this->Write(5,$e);
			}
			else
			{
				// Etiqueta
				if($e[0]=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					// Extraer atributos
					$a2 = explode(' ',$e);
					$tag = strtoupper(array_shift($a2));
					$attr = array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
						$attr[strtoupper($a3[1])] = $a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}
	
	function OpenTag($tag, $attr)
	{
		// Etiqueta de apertura
		if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,true);
		if($tag=='A')
		$this->HREF = $attr['HREF'];
		if($tag=='BR')
		$this->Ln(5);
	}
	
	function CloseTag($tag)
	{
		// Etiqueta de cierre
		if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,false);
		if($tag=='A')
		$this->HREF = '';
	}
	
	function SetStyle($tag, $enable)
	{
		// Modificar estilo y escoger la fuente correspondiente
		$this->$tag += ($enable ? 1 : -1);
		$style = '';
		foreach(array('B', 'I', 'U') as $s)
		{
			if($this->$s>0)
			$style .= $s;
		}
		$this->SetFont('',$style);
	}
	
	function PutLink($URL, $txt)
	{
		// Escribir un hiper-enlace
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
	
	function SetLineStyle($style) {
		extract($style);
		if (isset($width)) {
			$width_prev = $this->LineWidth;
			$this->SetLineWidth($width);
			$this->LineWidth = $width_prev;
		}
		if (isset($cap)) {
			$ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
			if (isset($ca[$cap]))
			$this->_out($ca[$cap] . ' J');
		}
		if (isset($join)) {
			$ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
			if (isset($ja[$join]))
			$this->_out($ja[$join] . ' j');
		}
		if (isset($dash)) {
			$dash_string = '';
			if ($dash) {
				$tab = explode(',', $dash);
				$dash_string = '';
				foreach ($tab as $i => $v) {
					if ($i > 0)
					$dash_string .= ' ';
					$dash_string .= sprintf('%.2F', $v);
				}
			}
			if (!isset($phase) || !$dash)
			$phase = 0;
			$this->_out(sprintf('[%s] %.2F d', $dash_string, $phase));
		}
		if (isset($color)) {
			list($r, $g, $b) = $color;
			$this->SetDrawColor($r, $g, $b);
		}
	}
	
	// Draws a line
	// Parameters:
	// - x1, y1: Start point
	// - x2, y2: End point
	// - style: Line style. Array like for SetLineStyle
	function Line($x1, $y1, $x2, $y2, $style = null) {
		if ($style)
		$this->SetLineStyle($style);
		parent::Line($x1, $y1, $x2, $y2);
	}
	
	// Draws a rectangle
	// Parameters:
	// - x, y: Top left corner
	// - w, h: Width and height
	// - style: Style of rectangle (draw and/or fill: D, F, DF, FD)
	// - border_style: Border style of rectangle. Array with some of this index
	//   . all: Line style of all borders. Array like for SetLineStyle
	//   . L: Line style of left border. null (no border) or array like for SetLineStyle
	//   . T: Line style of top border. null (no border) or array like for SetLineStyle
	//   . R: Line style of right border. null (no border) or array like for SetLineStyle
	//   . B: Line style of bottom border. null (no border) or array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
			$border_style = null;
			parent::Rect($x, $y, $w, $h, $style);
			break;
			case 'DF': case 'FD':
			if (!$border_style || isset($border_style['all'])) {
				if (isset($border_style['all'])) {
					$this->SetLineStyle($border_style['all']);
					$border_style = null;
				}
			} else
			$style = 'F';
			parent::Rect($x, $y, $w, $h, $style);
			break;
			default:
			if (!$border_style || isset($border_style['all'])) {
				if (isset($border_style['all']) && $border_style['all']) {
					$this->SetLineStyle($border_style['all']);
					$border_style = null;
				}
				parent::Rect($x, $y, $w, $h, $style);
			}
			break;
		}
		if ($border_style) {
			if (isset($border_style['L']) && $border_style['L'])
			$this->Line($x, $y, $x, $y + $h, $border_style['L']);
			if (isset($border_style['T']) && $border_style['T'])
			$this->Line($x, $y, $x + $w, $y, $border_style['T']);
			if (isset($border_style['R']) && $border_style['R'])
			$this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
			if (isset($border_style['B']) && $border_style['B'])
			$this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
		}
	}
	
	// Draws a Bézier curve (the Bézier curve is tangent to the line between the control points at either end of the curve)
	// Parameters:
	// - x0, y0: Start point
	// - x1, y1: Control point 1
	// - x2, y2: Control point 2
	// - x3, y3: End point
	// - style: Style of rectangule (draw and/or fill: D, F, DF, FD)
	// - line_style: Line style for curve. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
			$op = 'f';
			$line_style = null;
			break;
			case 'FD': case 'DF':
			$op = 'B';
			break;
			default:
			$op = 'S';
			break;
		}
		if ($line_style)
		$this->SetLineStyle($line_style);
		
		$this->_Point($x0, $y0);
		$this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
		$this->_out($op);
	}
	
	// Draws an ellipse
	// Parameters:
	// - x0, y0: Center point
	// - rx, ry: Horizontal and vertical radius (if ry = 0, draws a circle)
	// - angle: Orientation angle (anti-clockwise)
	// - astart: Start angle
	// - afinish: Finish angle
	// - style: Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
	// - line_style: Line style for ellipse. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - nSeg: Ellipse is made up of nSeg Bézier curves
	function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
		if ($rx) {
			if (!(false === strpos($style, 'F')) && $fill_color) {
				list($r, $g, $b) = $fill_color;
				$this->SetFillColor($r, $g, $b);
			}
			switch ($style) {
				case 'F':
				$op = 'f';
				$line_style = null;
				break;
				case 'FD': case 'DF':
				$op = 'B';
				break;
				case 'C':
				$op = 's'; // small 's' means closing the path as well
				break;
				default:
				$op = 'S';
				break;
			}
			if ($line_style)
			$this->SetLineStyle($line_style);
			if (!$ry)
			$ry = $rx;
			$rx *= $this->k;
			$ry *= $this->k;
			if ($nSeg < 2)
			$nSeg = 2;
			
			$astart = deg2rad((float) $astart);
			$afinish = deg2rad((float) $afinish);
			$totalAngle = $afinish - $astart;
			
			$dt = $totalAngle/$nSeg;
			$dtm = $dt/3;
			
			$x0 *= $this->k;
			$y0 = ($this->h - $y0) * $this->k;
			if ($angle != 0) {
				$a = -deg2rad((float) $angle);
				$this->_out(sprintf('q %.2F %.2F %.2F %.2F %.2F %.2F cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
				$x0 = 0;
				$y0 = 0;
			}
			
			$t1 = $astart;
			$a0 = $x0 + ($rx * cos($t1));
			$b0 = $y0 + ($ry * sin($t1));
			$c0 = -$rx * sin($t1);
			$d0 = $ry * cos($t1);
			$this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
			for ($i = 1; $i <= $nSeg; $i++) {
				// Draw this bit of the total curve
				$t1 = ($i * $dt) + $astart;
				$a1 = $x0 + ($rx * cos($t1));
				$b1 = $y0 + ($ry * sin($t1));
				$c1 = -$rx * sin($t1);
				$d1 = $ry * cos($t1);
				$this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
				$this->h - (($b0 + ($d0 * $dtm)) / $this->k),
				($a1 - ($c1 * $dtm)) / $this->k,
				$this->h - (($b1 - ($d1 * $dtm)) / $this->k),
				$a1 / $this->k,
				$this->h - ($b1 / $this->k));
				$a0 = $a1;
				$b0 = $b1;
				$c0 = $c1;
				$d0 = $d1;
			}
			$this->_out($op);
			if ($angle !=0)
			$this->_out('Q');
		}
	}
	
	// Draws a circle
	// Parameters:
	// - x0, y0: Center point
	// - r: Radius
	// - astart: Start angle
	// - afinish: Finish angle
	// - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
	// - line_style: Line style for circle. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - nSeg: Ellipse is made up of nSeg Bézier curves
	function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
		$this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
	}
	
	// Draws a polygon
	// Parameters:
	// - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
	// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	// - line_style: Line style. Array with one of this index
	//   . all: Line style of all lines. Array like for SetLineStyle
	//   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
		$np = count($p) / 2;
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
			$line_style = null;
			$op = 'f';
			break;
			case 'FD': case 'DF':
			$op = 'B';
			break;
			default:
			$op = 'S';
			break;
		}
		$draw = true;
		if ($line_style)
		if (isset($line_style['all']))
		$this->SetLineStyle($line_style['all']);
		else { // 0 .. (np - 1), op = {B, S}
			$draw = false;
			if ('B' == $op) {
				$op = 'f';
				$this->_Point($p[0], $p[1]);
				for ($i = 2; $i < ($np * 2); $i = $i + 2)
				$this->_Line($p[$i], $p[$i + 1]);
				$this->_Line($p[0], $p[1]);
				$this->_out($op);
			}
			$p[$np * 2] = $p[0];
			$p[($np * 2) + 1] = $p[1];
			for ($i = 0; $i < $np; $i++)
			if (!empty($line_style[$i]))
			$this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
		}
		
		if ($draw) {
			$this->_Point($p[0], $p[1]);
			for ($i = 2; $i < ($np * 2); $i = $i + 2)
			$this->_Line($p[$i], $p[$i + 1]);
			$this->_Line($p[0], $p[1]);
			$this->_out($op);
		}
	}
	
	// Draws a regular polygon
	// Parameters:
	// - x0, y0: Center point
	// - r: Radius of circumscribed circle
	// - ns: Number of sides
	// - angle: Orientation angle (anti-clockwise)
	// - circle: Draw circumscribed circle or not
	// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	// - line_style: Line style. Array with one of this index
	//   . all: Line style of all lines. Array like for SetLineStyle
	//   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
	// - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
	// - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
	function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
		if ($ns < 3)
		$ns = 3;
		if ($circle)
		$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
		$p = null;
		for ($i = 0; $i < $ns; $i++) {
			$a = $angle + ($i * 360 / $ns);
			$a_rad = deg2rad((float) $a);
			$p[] = $x0 + ($r * sin($a_rad));
			$p[] = $y0 + ($r * cos($a_rad));
		}
		$this->Polygon($p, $style, $line_style, $fill_color);
	}
	
	// Draws a star polygon
	// Parameters:
	// - x0, y0: Center point
	// - r: Radius of circumscribed circle
	// - nv: Number of vertices
	// - ng: Number of gaps (ng % nv = 1 => regular polygon)
	// - angle: Orientation angle (anti-clockwise)
	// - circle: Draw circumscribed circle or not
	// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	// - line_style: Line style. Array with one of this index
	//   . all: Line style of all lines. Array like for SetLineStyle
	//   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
	// - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
	// - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
	function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
		if ($nv < 2)
		$nv = 2;
		if ($circle)
		$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
		$p2 = null;
		$visited = null;
		for ($i = 0; $i < $nv; $i++) {
			$a = $angle + ($i * 360 / $nv);
			$a_rad = deg2rad((float) $a);
			$p2[] = $x0 + ($r * sin($a_rad));
			$p2[] = $y0 + ($r * cos($a_rad));
			$visited[] = false;
		}
		$p = null;
		$i = 0;
		do {
			$p[] = $p2[$i * 2];
			$p[] = $p2[($i * 2) + 1];
			$visited[$i] = true;
			$i += $ng;
			$i %= $nv;
		} while (!$visited[$i]);
		$this->Polygon($p, $style, $line_style, $fill_color);
	}
	
	// Draws a rounded rectangle
	// Parameters:
	// - x, y: Top left corner
	// - w, h: Width and height
	// - r: Radius of the rounded corners
	// - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
	// - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
	// - border_style: Border style of rectangle. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
		if ('0000' == $round_corner) // Not rounded
		$this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
		else { // Rounded
			if (!(false === strpos($style, 'F')) && $fill_color) {
				list($red, $g, $b) = $fill_color;
				$this->SetFillColor($red, $g, $b);
			}
			switch ($style) {
				case 'F':
				$border_style = null;
				$op = 'f';
				break;
				case 'FD': case 'DF':
				$op = 'B';
				break;
				default:
				$op = 'S';
				break;
			}
			if ($border_style)
			$this->SetLineStyle($border_style);
			
			$MyArc = 4 / 3 * (sqrt(2) - 1);
			
			$this->_Point($x + $r, $y);
			$xc = $x + $w - $r;
			$yc = $y + $r;
			$this->_Line($xc, $y);
			if ($round_corner[0])
			$this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
			else
			$this->_Line($x + $w, $y);
			
			$xc = $x + $w - $r ;
			$yc = $y + $h - $r;
			$this->_Line($x + $w, $yc);
			
			if ($round_corner[1])
			$this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
			else
			$this->_Line($x + $w, $y + $h);
			
			$xc = $x + $r;
			$yc = $y + $h - $r;
			$this->_Line($xc, $y + $h);
			if ($round_corner[2])
			$this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
			else
			$this->_Line($x, $y + $h);
			
			$xc = $x + $r;
			$yc = $y + $r;
			$this->_Line($x, $yc);
			if ($round_corner[3])
			$this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
			else {
				$this->_Line($x, $y);
				$this->_Line($x + $r, $y);
			}
			$this->_out($op);
		}
	}
	
	/* PRIVATE METHODS */
	
	// Sets a draw point
	// Parameters:
	// - x, y: Point
	function _Point($x, $y) {
		$this->_out(sprintf('%.2F %.2F m', $x * $this->k, ($this->h - $y) * $this->k));
	}
	
	// Draws a line from last draw point
	// Parameters:
	// - x, y: End point
	function _Line($x, $y) {
		$this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $y) * $this->k));
	}
	
	// Draws a Bézier curve from last draw point
	// Parameters:
	// - x1, y1: Control point 1
	// - x2, y2: Control point 2
	// - x3, y3: End point
	function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
	}
	
	
}
?>