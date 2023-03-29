<?php
$contacts = D2U_Immo\Contact::getAll();
$selected_contact_id = 'REX_VALUE[2]';
?>

<div class="row">
	<div class="col-xs-4">
		Kontaktperson der Immobilie angezeigen?
	</div>
	<div class="col-xs-8">
		<input <?php if ('REX_VALUE[1]' != 'Nein') {
        echo 'checked';
        } ?> type="radio" name="REX_INPUT_VALUE[1]" value="Ja">&nbsp;Ja&nbsp;&nbsp;&nbsp;
		<input <?php if ('REX_VALUE[1]' == 'Nein') {
        echo 'checked';
        } ?> type="radio" name="REX_INPUT_VALUE[1]" value="Nein">&nbsp;Nein
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">
		Standardkontakt als Fallback?
	</div>
	<div class="col-xs-8">
		<select name="REX_INPUT_VALUE[2]" class="form-control">
			<option value="0">Keine</option>
			<?php
            foreach ($contacts as $contact) {
                echo "<option value='". $contact->contact_id ."'". ($selected_contact_id == $contact->contact_id ? ' selected' : '') .'>'. $contact->lastname .', '. $contact->firstname .'</option>';
            }
            ?>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">
		Link zum Kontaktformular:
	</div>
	<div class="col-xs-8">
		REX_LINK[id=1 widget=1]
	</div>
</div>