<?php
$contacts = Contact::getAll();
$selected_contact_id = "REX_VALUE[2]";
?>

<p>Soll die Kontaktperson passend zur Immobilie der Seite angezeigt werden?
	<input <?php if("REX_VALUE[1]" != "Nein") print 'checked'; ?> type="radio" name="REX_INPUT_VALUE[1]" value="Ja">Ja
	<input <?php if("REX_VALUE[1]" == "Nein") print 'checked'; ?> type="radio" name="REX_INPUT_VALUE[1]" value="Nein">Nein</p>
<br />
<p>Falls nicht m&ouml;glich oder nicht gew&uuml;nscht, welche Kontaktperson
	soll angezeigt werden?</p>
<p><select name="REX_INPUT_VALUE[2]">
	<?php
	foreach($contacts as $contact) {
		print "<option value='". $contact->contact_id ."'". ($selected_contact_id == $contact->contact_id ? " selected" : "") .">". $contact->lastname .", ". $contact->firstname ."</option>";
	}
	?>
</select></p>
<p>Bitte geben Sie den Link zum Kontaktformular an: REX_LINK[id=1 widget=1]</p>