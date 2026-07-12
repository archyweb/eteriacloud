<?php

$this->current_session_id=$session_id;
$this->subscriber_id=$subscriber_id;

$physical_path = $this->app->subdomain_root . '/assets/documents/events/';
$physical_filename_with_background = 'event_'.$event_id.'_session_'.$this->current_session_id.'_all';
//$physical_filename_standard = 'event_'.$event_id.'_'.$this->data->subscriber_id;

$this->session=mysqli_fetch_object($this->db->query("select * from archy_events_sessions where event_id=".$event_id." AND id=".$this->current_session_id)); // prendo i dati della sessione corrente
$this->location=mysqli_fetch_object($this->db->query("select * from archy_events_locations where id=".$session_row->location_id));

$this->registry=mysqli_fetch_object($this->db->query("select * from archy_registry where id=".$this->subscriber_id)); // prendo i dati dell'anagrafica
$this->registry_city=mysqli_fetch_object($this->db->query("select * from archy_cities where id=".$this->registry->city_id));

$this->membership=mysqli_fetch_object($this->db->query("select * from archy_memberships where registry_id=".$this->subscriber_id));

$event=new Event();
$speaker=$event->get_session_speeches($this->current_session_id);
