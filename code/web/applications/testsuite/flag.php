<?php

function give_flag() {
	if (isset($_GET['bar']) && $_GET['bar']==37) { //37
		if($_GET['haz'] == 42) {
			echo "FLAG FLAG FLAG";
		}
	}
}