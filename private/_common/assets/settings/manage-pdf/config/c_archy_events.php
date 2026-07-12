<?php

$this->data=mysqli_fetch_object($this->db->query("select * from ".$this->table_intent." where id=".$this->data_id." AND subscriber_id=".$this->subscriber_id)); // prendo i dati del subscriber

$physical_path = $this->app->subdomain_root . '/assets/documents/events/';
$physical_filename_with_background = 'event_'.$this->data->event_id.'_session_'.$this->current_session_id.'_'.$this->subscriber_id;
$physical_filename_standard = 'event_'.$this->data->event_id.'_'.$this->subscriber_id;

$this->event=mysqli_fetch_object($this->db->query("select * from ".$this->table_main_intent." where id=".$this->data->event_id)); // prendo i dati dell'evento
$this->session=mysqli_fetch_object($this->db->query("select * from archy_events_sessions where event_id=".$this->data->event_id." AND id=".$this->current_session_id)); // prendo i dati della sessione corrente
$this->location=mysqli_fetch_object($this->db->query("select * from archy_events_locations where id=".$this->session->location_id));
$this->registry=mysqli_fetch_object($this->db->query("select * from archy_registry where id=".$this->subscriber_id)); // prendo i dati dell'anagrafica
$this->registry_city=mysqli_fetch_object($this->db->query("select * from archy_cities where id=".$this->registry->city_id));
$this->membership=mysqli_fetch_object($this->db->query("select * from archy_memberships where registry_id=".$this->subscriber_id));

$this->event_credits=$this->event->minimum_credits;
$this->accrued_credits=$this->data->accrued_credits;

$event=new Event();
$speaker=$event->get_session_speeches($this->current_session_id);
