<?php
class Calendar{
	
	public $app, $db;
	
	public function __construct(){
		
		global $app;
		$this->app=$app;
		$this->db=$app->get_db();
		
		$this->months=['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
		
	}
	
	public function add($event_details){
		$this->db->query("
			insert into archy_calendar 
			(calendar_start_date,calendar_end_date,calendar_title,calendar_description,calendar_allday,calendar_class) 
			values 
			('".$event_details['start']."','".$event_details['end']."','".addslashes($event_details['title'])."','".addslashes($event_details['description'])."','".$event_details['allDay']."','".$event_details['className']."')
		");
		return $this->db->insert_id;
	}
	
	/*public function print_month($month){
		
		$calendar_month=new DateTime(date('Y').'-'.date('m').'-01');
		
		if($month!=''):
			$relative_month=$month;
			$calendar_month->modify($relative_month." month");
		else:
			$relative_month=0;
		endif;
		
		$previous=$relative_month-1;
		$next=$relative_month+1;
		
		$month_name=$this->months[$calendar_month->format('n')-1];
		$month_days=$calendar_month->format('t');
		$year=$calendar_month->format('Y');
		$previous_month_days=$calendar_month->format('N')>1? $calendar_month->format('N')-1 : 0;
		$today=new DateTime();
		
		?>
      <div class="row" style="margin-bottom:12px;">
         <div class="col-sm-6">
            <h4 id="calendar_label"><?php echo $month_name.' '.$year?></h4>
         </div>
         <div class="col-sm-6">
            <div id="calendar_buttons">
               <div class="btn-group" role="group">
                  <a class="btn btn-default" href="/archy/app.php?view=dashboard&m=<?php echo $previous?>" role="button"><i class="glyphicon glyphicon-chevron-left"></i></a>
                  <a class="btn btn-default" href="/archy/app.php?view=dashboard" role="button">Oggi</a>
                  <a class="btn btn-default" href="/archy/app.php?view=dashboard&m=<?php echo $next?>" role="button"><i class="glyphicon glyphicon-chevron-right"></i></a>
               </div>
               <a class="btn btn-primary tt" title="Aggiungi evento" data-toggle="modal" data-target="#calendarModal" href="javascript:void(0)" role="button"><i class="glyphicon glyphicon-plus"></i></a>
            </div>
         </div>
      </div>
      <div class="table-responsive">
         <table cellpadding="0" cellspacing="0" id="calendar">
            <tr>
               <th>Lun</th><th>Mar</th><th>Mer</th><th>Gio</th><th>Ven</th><th>Sab</th><th>Dom</th>
            </tr>
            <?php
            $day_count=0;
            $calendar_month->modify("-".$previous_month_days." day");
            for($row=1; $row<=6; $row++):
               ?><tr><?php
               for($col=1; $col<=7; $col++):
                  $day_num=$calendar_month->format('d');
                  if($row==1 and $col<=$previous_month_days):
                     $class='past';
                  else:
                     $class=$day_count<$month_days? '' : 'future';
                     $day_count++;
                  endif;
                  if($calendar_month->format('ymd')==$today->format('ymd')):
                     $class.=' today';
                  endif;
                  ?>
                  
                  <td class="<?php echo $class?>">
                     <?php 
                     echo '<i>'.$day_num.'</i>';
                     $this->print_scheduled_by_date($calendar_month->format('Y-m-d'));
                     ?>
                  </td>
                  
                  <?php
                  $calendar_month->modify("+1 day");
               endfor;
               ?></tr><?php
            endfor;
            ?>
         </table>
      </div>
      
      <!-- Modal -->
      <div class="modal fade" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="calendarModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form role="form" class="validate" method="post" action="/archy/app.php">
               <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="calendarModalLabel">Aggiungi al calendario</h4>
               </div>
               <div class="modal-body">
                  
						<div class="form-group">
							<label for="calendar_date">Data <span class="glyphicon glyphicon-asterisk text-info" data-toggle="tooltip" title="Campo richiesto"></span></label>
							<div class="input-group">
								<input type="text" class="form-control date required" id="calendar_date" name="main[date]" placeholder="__/__/____" maxlength="10">
								<div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
							</div>
						</div>
                  
                  <label>Priorità</label>
                  <div class="radio centered" style="margin-top:0">
                  	<label class="priority bg-primary"><input type="radio" name="main[css_class]" value="text-primary" checked></label>
                  	<label class="priority bg-danger"><input type="radio" name="main[css_class]" value="text-danger"></label>
                  	<label class="priority bg-warning"><input type="radio" name="main[css_class]" value="text-warning"></label>
                  	<label class="priority bg-success"><input type="radio" name="main[css_class]" value="text-success"></label>
                  </div>
                                    
						<div class="form-group">
							<label for="calendar_event">Evento <span class="glyphicon glyphicon-asterisk text-info" data-toggle="tooltip" title="Campo richiesto"></span></label>
                     <textarea rows="4" cols="80" class="form-control required" name="main[event]"></textarea>
						</div>
                  
                  <input type="hidden" name="main[icon]" value="calendar">
                  <input type="hidden" name="main[title]" value="Evento">
                  <input type="hidden" name="do_action" value="store">
                  <input type="hidden" name="action" value="insert into">
                  <input type="hidden" name="intent" value="archy_calendar">
                  <input type="hidden" name="goto" value="/archy/app.php?view=dashboard">
                  <input type="hidden" name="intent_id" value="">
                  <input type="hidden" name="intent_name" value="Evento">
               </div>
               <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
                 <button type="submit" class="btn btn-primary">Salva</button>
               </div>
            </form>
          </div>
        </div>
      </div>
      
      <?php
		
	}
	
	public function print_scheduled_by_date($db_date){
		
		$events=$this->db->query("select event,title,css_class,icon from archy_calendar where date='".$db_date."'");

		while($event=mysqli_fetch_object($events)):
			?><span class="glyphicon glyphicon-<?php echo $event->icon?> <?php echo $event->css_class?>" data-toggle="popover" title="<?php echo $event->title?>" data-content="<?php echo $event->event?>"></span><?php
		endwhile;		
		
	}*/
	
        public function get(){
            $events = array();
            $rows=$this->db->query("select * from archy_calendar");
                $i = 0;
		while($event=mysqli_fetch_object($rows)):
                                    
			$e = array(
                        'id' => $event->id,
                        'title' => $event->calendar_title,            
                        'start' => $event->calendar_start_date ,//$start,
                        'end' => $event->calendar_end_date,
                        'allDay' => $event->calendar_allday,
                        'className' => $event->calendar_class,
                        'description' => $event->calendar_description
                    );
                    $events[$i] = $e;
                    $i++;
                endwhile;
                
            return json_encode($events);
        }
        
}
