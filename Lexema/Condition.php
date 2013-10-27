<?php
class Lexema_Condition extends Lexema_Tag {

	public function parse($data) {
		if(preg_match('/(\w+)\s*([=><])?\s*(["\'\w]+)?/', $this->getParamsString(), $m)) {
			$varname = $m[1];
			$varvalue = isset($data[$varname]) ? $data[$varname] : "";
			$varvalue = empty($varvalue) ? '0' : $varvalue;
			
			if(isset($m[2])) {
				$sign = $m[2];
				$valueName = $m[3];
				
				if(preg_match('/["\'](\w+)["\']/', $valueName)) {
					/* '������' ��� "������" */
					$value = $valueName;
				} else {					
					if(is_numeric($valueName)) {
						/* 5 ��� 10 ��� 7.5 */
						$value = $valueName;
					} elseif(isset($data[$valueName])) {
						/* age=25 ��� name="������"  - ���� �����, �� ������� �� �����, ���� ������, �� ����� */
						if(is_numeric($data[$valueName])) {
							$value = $data[$valueName];
						} elseif (is_string($data[$valueName])) {
							$value = '"'.$data[$valueName].'"';
						} else {
							/* ��� ������ �������, �������... �� �� �� �� ������������ */
							$value = '"'.gettype($data[$valueName]).'"';
						}
					} else {
						$value = '"'.$valueName.'"';
					}
				}
				$condition = "$varvalue $sign $value";
			} else {
				$condition = isset($data[$varname]) ? !empty($data[$varname]) : "false";
				$condition = $condition ? $condition : "false";
			}
			
			$phpCode = "return $condition ? 1 : 0;";
			
			$conditionResult = eval($phpCode);
			
			$html = "";
			
			/* �������� ��� ������� �� ��, ������� ������ ����������� � ������ ������ � � ������ ������� */
			$conditionTags = array(
				'true' => array(), 
				'false' => array()
			);
			
			$tagsName = "true";
			
			foreach ($this->getTags() as $tag) {
				if($tag->getName() == 'else') {
					$tagsName = 'false';
				}
				$conditionTags[$tagsName][] = $tag;
			}
			
			/* �������� ������� �������� ������������ ������� ��������� if */
			if($conditionResult) {
				$tags = $conditionTags['true'];
			} else {
				$tags = $conditionTags['false'];
			}
			
			foreach ($tags as $tag) {
				$html .= $tag->parse($data);
			}
			
			return $html;
		}
	}
	
}

?>