<?php
class pagination {
    var $p, $max_r, $limits, $base_link;
    var $count_all = 0, $sql, $totalres, $totalpages;
	
    function __construct($base_link) {
        $this->base_link = $base_link;
    }
	
    function setMax($max_r) {
        $this->p = ((isset($_GET['p'])) ? $_GET['p'] : 1);
        $this->max_r = $max_r;
        $this->limits = ($this->p - 1) * $this->max_r;
    }
	
    function setData($query) {
		global $connection;
        $this->sql = mysqli_query($connection, $query." LIMIT {$this->limits},{$this->max_r}") or die(mysql_error());
        $this->totalres = mysqli_query($connection, $query) or die(mysql_error());
		$this->count_all = mysqli_num_rows($this->totalres);
        $this->totalpages = ceil($this->count_all / $this->max_r);
    }
	
    function show_all() {
		global $connection;
        $fields = mysqli_num_fields($this->totalres);
        while ($row = mysqli_fetch_row($this->sql))   {
            echo '<tr>';
            for ($f=0; $f < $fields; $f++)   {
                echo '<td>'.$row[$f].'</td>';
            }
            echo '</tr>';
        }
    }
	
    function displayLinks($show = 10){
		$current_page = ((isset($_GET['p'])) ? $_GET['p'] : 1);
		echo (($this->p > 1)? '<a href="'.$this->base_link.'&p=1"> [First] </a> ': '');
		
        if($this->p != 1) {
            $previous = $this->p-1;
            echo '<a href="'.$this->base_link.'&p='.$previous.'"> [Previous] </a>';
        }
		
        for($i =1; $i <= $show; $i++) {
			echo (($this->p < $this->totalpages) ? ' <a href="'.$this->base_link.'&p='.$this->p.'">'.(($current_page == $this->p) ? '<b>'.$this->p.'</b>' : $this->p).'</a>': '');
            $this->p++;
        }
		
        echo '...';
		
		echo (($this->p != $this->totalpages)? '<a href="'.$this->base_link.'&p='.$this->totalpages.'"> '.$this->totalpages.' </a>' : '');
		
        if($current_page < $this->totalpages) {
			echo '<a href="'.$this->base_link.'&p='.($current_page + 1).'"> [Next] </a>';
        }
    }
}