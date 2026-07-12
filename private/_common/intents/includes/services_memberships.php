<?php if (isset($_GET['id'])) : ?>
<span class="text-bg-primary px-3 py-2 text-uppercase">Riepilogo quote</span>
<?php
$service_membership=new ServiceMembership($_REQUEST['id']);
echo "<p><b>Stato:</b> ".$service_membership->statuses[$service_membership->membership->status]."</p>";
echo "<p><b>Inizio validità servizio:</b> ".App::latin_date($service_membership->membership->membership_begin)."</p>";
echo "<p><b>Scadenza servizio:</b> ".App::latin_date($service_membership->membership->membership_end)."</p>";
//var_dump($service_membership);
echo "<hr>";
//print_r(json_encode($service_membership->get_service_subscription_list()));
?>
<?php endif ?>

<script type="text/javascript">

window.onload=function(){
    $('input[name="main[registry_id]"]').on('input',function(){
        var registry_id=$('input[name="main[registry_id]"]').val();
        var services_id=$('input[name="main[services_id]"]').val();
        $.ajax({
            type: "POST",
            url: "/archy/app.php?do_action=check_duplicate_service_membership",
            data: 'registry_id='+registry_id+'&services_id='+services_id,
            dataType: 'json',
            cache: false,
            success: function(response){
                console.log(response);
                if(response.is_already_member){
                    alert('"'+$('#archy_services_memberships_registry_id').val()+'" è già iscritto al servizio "'+$('#archy_services_memberships_services_id').val()+'".');
                    $('#archy_services_memberships_registry_id, input[name="main[registry_id]"]').val('');
                }
            }
        });
    });

    $('input[name="main[services_id]"]').on('input',function(){
        var registry_id=$('input[name="main[registry_id]"]').val();
        var services_id=$('input[name="main[services_id]"]').val();
        $.ajax({
            type: "POST",
            url: "/archy/app.php?do_action=check_duplicate_service_membership",
            data: 'registry_id='+registry_id+'&services_id='+services_id,
            dataType: 'json',
            cache: false,
            success: function(response){
                console.log(response);
                if(response.is_already_member){
                    alert('"'+$('#archy_services_memberships_registry_id').val()+'" è già iscritto al servizio "'+$('#archy_services_memberships_services_id').val()+'".');
                    $('#archy_services_memberships_services_id, input[name="main[services_id]"]').val('');
                }
            }
        });
    });

}

</script>
    