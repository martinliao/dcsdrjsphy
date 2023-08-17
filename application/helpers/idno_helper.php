<?php 

class Idno{
	/*
		檢查原始身分證
	*/
	public static function checkNumber($idno)
	{
		if (strlen($idno) != 10 || !preg_match("/^[A-Z](1|2|8|9|A|B|C|D)\d{8}$/i", $idno)){
			return false;
		}

		$areaList = Idno::getAreaList();
		$firstChar = substr($idno, 0, 1);
		$secondChar = substr($idno, 1, 1);

		if (in_array($secondChar, ['A', 'B', 'C', 'D'])){
			$secondChar = $areaList[$secondChar];
			$secondChar = substr((string)$secondChar, 1, 1);
			$idno = substr($idno, 0, 1).$secondChar.substr($idno, 2);
		}

		if (isset($areaList[$firstChar])){
			$firstnumber = $areaList[$firstChar];
			$sec = (($firstnumber % 10) * 9 + floor($firstnumber / 10)) % 10;
			$tmpidno = $sec.substr($idno, 1);
			$total = $sec;
			// 加權分數  1 8 7 6 5 4 3 2 1 1
			for($i=8;$i>0;$i--){
				$total += $tmpidno[9-$i] * $i; 
			}
			$total += $tmpidno[9];
			$total = $total % 10;
			return $total == 0;
		}else{
			return false;
		}
	}	

	/*
		區域碼
	*/
	public static function getAreaList()
	{
		return array(
			'A' => 10,
			'B' => 11,
			'C' => 12, 
			'D' => 13, 
			'E' => 14, 
			'F' => 15, 
			'G' => 16, 
			'H' => 17, 
			'I' => 34, 
			'J' => 18, 
			'K' => 19, 
			'L' => 20, 
			'M' => 21, 
			'N' => 22, 
			'O' => 35, 
			'P' => 23, 
			'Q' => 24, 
			'R' => 25, 
			'S' => 26, 
			'T' => 27, 
			'U' => 28, 
			'V' => 29, 
			'W' => 32, 
			'X' => 30, 
			'Y' => 31, 
			'Z' => 33
		);	
	}
}