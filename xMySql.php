<?php
/**
 * This class was intended to make my life to writing SQL statements a sinch.
 * The programmer only has to worry about passing the data. No more writing SQL!
 * I know there are other alternatives, but I created this for the challenge of making it. 
 * and it works perfectly for what i need to to :)
 * 
 * @author Xopher Pollard
 * @email heylisten@xtiv.net
 * @version v1.1.2
 */

if(!class_exists('xMySql')){
	class xMySql {
        var $mConn;     // MySQL link identifier 
        var $mSql;      // Stores last SQL statement
        var $mData;     // Result Data as Array         
        
        var $mTables;	// A list of all the tables.
        
        var $mBy;		// By as Array ('field'=>'DESC')
        var $mLimit;    // Limit as Array(start,limit)
		var $PREFIX; 	// Prefix for data_table 

        var $ERROR = false;
				       
        function __construct($host,$user,$pass,$database,$prefix=null){
            $this->error = &$this->ERROR;
            try{
            	if($prefix){ 
	            	$prefix = str_replace('.','_',$prefix).'_';
					$www = explode('_',$prefix);
					if($www[0] == 'www'){
						unset($www[0]);
						//$prefix = implode('_',$www);
					}	
					$prefix = ($prefix != '_')? $prefix : null;
            	}
				
				$this->PREFIX = ($prefix) ? $prefix : null;
            	
            	
            	$this->db = array(
            		'host'		=> $host,
            		'user'		=> $user,
            		//'pass'		=> $pass,
            		'database'	=> $database,
            		'prefix'	=> $prefix
            	);
            	
            	
            	
            	/*if(isset($_SESSION['sql_conn'])$_SESSION['sql_conn'][$this->db['host']]){
            		$this->mConn = $_SESSION['sql_conn'][$this->db['host']];	
            	} */
            	
            	if(!$this->mConn = mysql_connect($this->db['host'], $this->db['user'], $pass))
            		throw new Exception( mysql_error() );

            	$_SESSION['sql_conn'][$this->db['host']] = $this->mConn;	
            	// Select DB if given
                if ($this->db['database'] != "")
                   $this->DB( $this->db['database'] );                   
            }
            catch(Exception $e)
            {
                $this->error = 'Connection Failed '.$e->getMessage();
            }
    	}
    	
    	function setStartLimit($start=0,$limit=0){
    		$this->mLimit = array('start' => $start,'limit' => $limit);
    	}
    	
    	function DB($db){
    		mysql_select_db($db,$this->mConn);   
    	}
    	
    	function Q($sql){
    		$this->mSql 	= $sql;
    		$r 				= mysql_query($this->mSql,$this->mConn);
    		$this->errno 	= mysql_errno($this->mConn);
    		
    		switch($r){
    		// MySQL error
    			case(false):
    				$this->error = mysql_error();
                    $this->sql     = $sql;
    				return false; //die(mysql_error()."<br/>".$this->mSql);
    			break;
    			case($r === true):
    				$this->msg = "ran ".$sql;
    				return true;
    			break;
    		// MySQL Success	
    			default:
    				$this->mCount       = mysql_num_rows($r);
		    		$this->mData = null;
		    		while ($row = mysql_fetch_array($r, MYSQL_ASSOC)) {
					    $this->mData[] = $row;
					}
    				mysql_free_result($r);
    				return $this->mData; 
    			break;
    		}
    	}
    	function ListTables(){
    		$q = $this->Q("SHOW TABLES");
    		
    		if(!empty($q)){
    			foreach($q as $v){
    				foreach($v as $t){
    					$this->mTables[$t] = (isset($this->mTables[$t]))? $this->mTables[$t] : null;//$this->ListColumns($t);
    				}
    			}
    			return $this->mTables;
    		}
    		
    		
    	}
    	
    	function ListColumns($table){
    		$cols = $this->Q("SHOW COLUMNS FROM `$this->PREFIX$table`");
    		if(!$cols || $this->errno == 1146){
    			return false;	
    		}
    		if(count($cols) > 1){
	    		foreach($cols as $v){
	    			if($v['Field'] != ""){
	    				foreach($v as $k => $o){
	    					if($k !="Field"){
	    						$row[$v['Field']][$k] = $o;	
	    					}
	    				}	
	    			}
				}
				$this->mTables[$table] = $row;
				return $row;	
    		}
    	}
    	
    	function Count($table,$needle,$o="=",$aor="AND"){
    		$sql = "SELECT Count('id') FROM $this->PREFIX$table";
    		if($needle){
    			$sql .= $this->Where($needle,$o,$aor);
    		}
    		$num = $this->Q($sql);
    		return $num[0]["Count('id')"];
    	}	


        function CountQ($sql){
            $sql = "SELECT Count('*') FROM ($sql) as count_t";
            // if($needle){
            //     $sql .= $this->Where($needle,$o,$aor);
            // }
            $num = $this->Q($sql);
            return $num[0]["Count('*')"];
        }   
    
    	/**
    	 * @desc Selects row(s) of data. returns an associative array.
    	 * @param $data
    	 * @param $from
    	 * @param $needle
    	 * @param $join
    	 * @param $o
    	 * @param $aor
    	 */
    	function Select($data="*",$from,$needle=NULL,$join="LEFT",$o="=",$aor="AND",$groupby=null){


            // check to make sure the table exists. 
            $tables = $this->ListTables();

            if($tables != null && !is_array($from)){
            	if(!in_array( $this->PREFIX.$from, array_keys($tables)))
            		return false;
            }else{
                //	return false;
            }
            
            



    		if(is_array($data)){
	    		
	    		foreach($data as  $c => $t){
	    			foreach($t as $k => $v){
		    			if(!is_int($k)){
		    				$v = "$k as $v";
		    			}
		    			$select .= ($select) ? ", $this->PREFIX$c.$v" : "$this->PREFIX$c.$v";	
	    			}
	    			
	    			/*if(isset($from[$c]) && is_array($from)){
	    				$v = current($from[$c]);
	    				$f .= "$join JOIN $c ON $c.$v = ".key($from[$c]).".$v";
	    			}else{
	    				$f .= " $c ";
	    			}*/
	    		}
                $data = $select;

    		}

            $f = " FROM ";

            if(is_array($from)){
                 $find = key($from);
                    next($from);
                 $link = key($from);
                
                $f .= "$this->PREFIX$find $join JOIN $this->PREFIX$link ON $this->PREFIX$link.$from[$link] = $this->PREFIX$find.$from[$find]";
            }else{
                $f .= "$this->PREFIX$from ";
            }
            
            $select = $data.$f;

    		
    		$sql = "SELECT $select ";
    		
    		if($needle){
    			$sql .= $this->Where($needle,$o,$aor);
    		}

            if($groupby){
                $sql .= " GROUP BY $groupby";
            }
    		
    		
    		if($this->mBy){
    			$sql .= $this->Order($this->mBy);
    			$this->mBy = null;
    		}
    		
    		if(is_array($this->mLimit)){
				$l = $this->mLimit;
				$sql .= " Limit $l[start],$l[limit]";
			}
    		
			// Counts All Records
    		//$this->mCountAll = $this->CountQ($sql);
    		
    		return $this->Q($sql);
    	}
    	
	    function Order($by){
	        if(is_array($by)){
	        	$sql = '';
	        	foreach($by as $k =>$v){
	        		$sql .= (!$sql)? "$k $v": ",$k $v";
	        	}
	        	return "ORDER BY $sql";
	        }else{
	        	return $by;
	        }
        }
    	
    	function Where($haystack,$o="=",$aor="AND"){
            $needle = null;
    		if(is_array($haystack)){
	            foreach($haystack as $c => $v){
	                if(is_array($v)){
	                	$c = key($v);
	                	$v = $v[$c];
	                }
	            	
	            	
	            	$v = mysql_real_escape_string($v);
	                
                    if(!is_numeric($v))
                         $v = ($o == "LIKE" || $o == "NOT LIKE") ? "'%$v%'" : "'$v'";    

	                $where = ($v == "null") ? "$c IS NULL" : "$c $o $v"; 
	                
	                $needle .= ($needle) ? " $aor $where " : " WHERE $where "; 
	            }	
            }else if(is_string($haystack)){
            	$needle = " WHERE $haystack ";
            }
            return $needle;
        }
    	
        
        
        function CreateTable($name,$fields){
        	$sql = "CREATE TABLE $this->PREFIX$name(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
        	foreach($fields as $k =>$v){      		
        		if(!is_int($k)){
        			$v = $k;
        		}
        		$sql .= ", `$v` varchar(255)"; 
        		
        		
        	}
        	$sql .= ")";
        	$this->Q($sql);
        }
        
    	function Insert($table,$columns){
    		if(!$this->ListColumns($table)){
    			$this->CreateTable($table,$columns);
    		}
    		$keys = null;
    		$values = null;
    		
    		foreach($columns as $k => $v){
    			$v       = ($v != '') ? mysql_real_escape_string($v) : '';
    			$keys    .= ($keys) ? ",`$k`" : "`$k`";
    			$values  .= ($values) ? ",'$v'" : "'$v'";
    		}
    		$this->Q("INSERT INTO $this->PREFIX$table ($keys) VALUES ($values)");
    		return mysql_insert_id($this->mConn);
    	}
    	
    	function Update($table,$set_col,$needle){
    		$set = '';
            foreach($set_col as  $c => $v){
                $v = mysql_real_escape_string($v);
                $q = ($c == $v) ? "`$c` = $c+1" : "`$c` = '$v'";	
                $set .= ($set) ? ", $q": $q;
            }
    		
    		return $this->Q("UPDATE $this->PREFIX$table SET $set ".$this->Where($needle));
    	}
    	
    	function Inc($table,$field,$i,$where){
    		$this->Q("UPDATE $this->PREFIX$table SET $field = $field + $i ".$this->Where($where));
    	}
    	
    	function Delete($table,$needle){
            return $this->Q("DELETE FROM $this->PREFIX$table ".$this->Where($needle));
        }
        
        function Debug(){
        	echo "<pre>";
        	print_r($this->mData);
        	echo "<pre>";
        }
        
        function Backup($file=false,$tables = '*'){
			/* backup the db OR just a table */
			
        	//get all of the tables
			if($tables == '*')
			{
				$tables = array();
				$result = mysql_query("SHOW TABLES LIKE '$this->PREFIX%';");
				while($row = mysql_fetch_row($result))
				{
					$tables[] = $row[0];
				}
			}
			else
			{
				$tables = is_array($tables) ? $tables : explode(',',$tables);
			}
			
			$md5hash 	= substr(md5($this->db['host'].$this->db['database']),0,5);
			$semicolon = ";#$md5hash#";
			
			//cycle through
			$return = '';
			foreach($tables as $table)
			{
				$result = mysql_query('SELECT * FROM '.$table);
				$num_fields = mysql_num_fields($result);
				
				// used for import feature.
				
				
				$return .= 'DROP TABLE '.$table.$semicolon;
				$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
				$return.= "\n\n".$row2[1]."$semicolon\n\n";
				
				for ($i = 0; $i < $num_fields; $i++) 
				{
					while($row = mysql_fetch_row($result))
					{
						$return.= 'INSERT INTO '.$table.' VALUES(';
						for($j=0; $j<$num_fields; $j++) 
						{
							$row[$j] = addslashes($row[$j]);
							$row[$j] = preg_replace( "/\n/" ," \\n ",$row[$j]);
							if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
							if ($j<($num_fields-1)) { $return.= ','; }
						}
						$return.= ")$semicolon\n";
					}
				}
				$return.="\n\n\n";
			}
			
			if($file){
				//save file
				$handle = fopen($file,'w+');
				fwrite($handle,$return);
				fclose($handle);
			}else{
				return $return;
			}
        }
        
        function ImportSql($filename,$key='#1861c#'){
        	$return = false;
			$sql_start = array('INSERT', 'UPDATE', 'DELETE', 'DROP', 'GRANT', 'REVOKE', 'CREATE', 'ALTER');
			$sql_run_last = array('INSERT');
			
			if (file_exists($filename)) {
				$sql = file_get_contents($filename);
				$queries = explode(";$key",$sql);
				
				foreach ($queries as $k => $to_run) {
					if(trim($to_run) != ""){						
						$this->Q($to_run);
					}
				}

                $db_name = $this->db['database'];
                $prefix  = $this->db['prefix'];
                
                // We need to rewrite the tables we just imported to have the proper prefix
                $tables  = $this->Q("Show Tables");

                foreach ($tables as $k => $value) {
                    foreach($value as $key => $t){
                        $rename[$t]['from'] = $t;
                        $rename[$t]['to'] = $prefix . $t;
                    }
                }


                foreach($rename as $k => $r){
                    $this->Q("RENAME TABLE $db_name.{$r['from']} TO $db_name.{$r['to']}");
                }

			}else{
				die("$filename File Doesn't Exist");
			}
        }
        
		
        
        function Close(){
        	mysql_close($this->mConn);
        }
        
    }	
}
?>
