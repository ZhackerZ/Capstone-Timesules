<script type="text/javascript">Timesules.cal.init();</script>
<?php
$this->load("main_header", $data);
$info = $data["monthInfo"];
$capsules = $data["capsules"];

$colors = Array("A23BDD","3cb878","e03c3f","0077a7");

?>
<div id="centerContent">
 <div id="calendarButtons">
  <a href="/calendar.php?m=<?php echo $info['lmon'][1].":".$info['lmon'][2]; ?>"><button class="toggle-button">&lt; <?php echo $info['lmon'][0]." ".$info['lmon'][2]; ?></button></a>
  <a href="/calendar.php?m=<?php echo $info['nmon'][1].":".$info['nmon'][2]; ?>" class="nextMonButton"><button class="toggle-button"><?php echo $info['nmon'][0]." ".$info['nmon'][2]; ?> &gt;</button></a>
 </div>
 <div id="calendar">
  <div id="monthHeader"><?php echo strtoupper($info["tmon"]); ?></div>
  <div id="calendarContent" class="table">
   <div id="dayHeadings" class="table-row header">
    <div class="dateBlock">Sunday</div>
    <div class="dateBlock">Monday</div>
    <div class="dateBlock">Tuesday</div>
    <div class="dateBlock">Wednesday</div>
    <div class="dateBlock">Thursday</div>
    <div class="dateBlock">Friday</div>
    <div class="dateBlock">Saturday</div>
<?php
$rows = floor(($info["itmon"][0]+$info["itmon"][1])/7); //#of days in month + #of extra days / 7, floor + 1
for($i=0;$i<$rows*7;$i++) {
	if($i==0 || $i%7==0)
		echo '   </div>'."\n".'   <div class="table-row">'."\n";
	if($i<$info["itmon"][1] || ($i-$info["itmon"][1]+1)>$info["itmon"][0])
		echo '    <div class="dateBlock"><span></span></div>'."\n";
	else {
		$date = ($i-$info["itmon"][1]+1);
		echo '    <div class="dateBlock">'."\n".'     <span>'.$date.'</span><br />'."\n";
		if(count($capsules[$date])>0) {
			foreach($capsules[$date] as $cap) {
				echo '     <div class="circles'.(($cap[3])?' released':'').'" style="background-color:#'.$colors[$cap[0]].'" id="dot-'.$cap[1].'"></div>
      <div class="hover" id="desp-'.$cap[1].'">
       <div>'.$cap[2].'</div>
      </div>
';
			}
		}
		echo '    </div>'."\n";
	}
}
?>
   </div>
<!--   <div class="table-row">
    <div class="dateBlock"><span>1</span></div>
    <div class="dateBlock"><span>2</span></div>
    <div class="dateBlock"><span>3</span></div>
    <div class="dateBlock"><span>4</span></div>
    <div class="dateBlock"><span>5</span></div>
    <div class="dateBlock"><span>6</span></div>
    <div class="dateBlock"><span>7</span></div>
   </div>
   <div class="table-row">
    <div><span>8</span></div>
    <div><span>9</span></div>
    <div>
     <span>10</span><br />
     <div class="circles" style="background-color:#e03c3f" id="dot1"></div>
     <div class="circles" style="background-color:#3cb878" id="dot2"></div>
    </div>
    <div><span>11</span></div>
    <div><span>12</span></div>
    <div><span>13</span></div>
    <div><span>14</span></div>
   </div>
   <div class="table-row">
    <div><span>15</span></div>
    <div>
     <span>16</span>
    </div>
    <div><span>17</span></div>
    <div><span>18</span></div>
    <div><span>19</span><br /><div class="circles" style="background-color:#0077a7;" id="dot3"></div></div>
    <div><span>20</span></div>
    <div><span>21</span></div>
   </div>
   <div class="table-row">
    <div>
     <span>22</span><br />
     <div class="circles" style="background-color:#3cb878" id="dot4"></div>
    </div>
    <div><span>23</span></div>
    <div><span>24</span></div>
    <div><span>25</span></div>
    <div><span>26</span></div>
    <div><span>27</span></div>
    <div><span>28</span></div>
   </div>
   <div class="table-row">
    <div><span>30</span></div>
    <div><span>31</span></div>
    <div><span>&nbsp;</span></div>
    <div><span>&nbsp;</span></div>
    <div><span>&nbsp;</span></div>
    <div><span>&nbsp;</span></div>
    <div><span>&nbsp;</span></div>
   </div>
-->
  </div>
  <div id="ledgend">
   <div><div class="circles" style="background-color:#<?php echo $colors[0]; ?>"></div> Group Release</div>
   <div><div class="circles" style="background-color:#<?php echo $colors[1]; ?>"></div> Personal Release</div>
   <div><div class="circles" style="background-color:#<?php echo $colors[2]; ?>"></div> Group Prompt Locked</div>
   <div><div class="circles" style="background-color:#<?php echo $colors[3]; ?>"></div> Personal Prompt Locked</div>
  </div>
  <!-- <div id="hover"><div>Timesule 1<br />From: Tyler Hadidon<br /><br />Breif Message</div></div> -->
 </div>
</div>
<?php
$this->load("right_sidebar", @array_merge(Array("closeAll"), is_array($data)?$data:Array()), FALSE);
$this->load("main_footer", $data, FALSE);
?>