<?php

if (!function_exists("cmsms")) exit;

if(empty($params['url']) || empty($params['lang_iso'])){
	//XXXXXXXXXXXXXXXXXXXXXX
}

//Let's retrieve the comments !
$example = new OrmExample();
$example->addCriteria('url', OrmTypeCriteria::$EQ, array($params['url']));
$example->addCriteria('lang_iso', OrmTypeCriteria::$EQ, array($params['lang_iso']));

$comments = OrmCore::findByExample(new CommentSkeleton(), $example);
$count = count($comments);

echo "<h2>Comments for the Url {$params['url']} ({$params['lang_iso']})</h2>";
echo "<table class='pagetable' cellspacing='0'><tr>
		<th>id</th>
		<th>text</th>
		<th>&nbsp;</th>
	</tr>";
if($count == 0){
	echo "<tr><td colspan='3'><center>no record in database</center></td></tr>";
}

foreach($comments as $comment){

		// We can easily get all the values with the $object->get('fieldname') syntax
		echo "<tr>
				<td>".$this->securize($comment->get('comment_id'))."</td>
				<td>".$this->securize($comment->get('text'))."</td>
				<td>".$this->CreateLink($id, 'editCommentDelete', $returnid, $img_delete,array('comment_id'=>$comment->get('comment_id'))).
				"</td>
			</tr>";
	}

echo "</table>";
echo "<p>There are " . $count . " CommentSkeleton(s) into the database for this URL / lang.</p>";

$comment = new CommentSkeleton();
$comment->set("url",$params['url']);
$comment->set("lang_iso",$params['lang_iso']);


$formStart = $this->CreateFormStart($id, 'editCommentSave', $returnid, 'post', '',false,'',array('url'=>$params['url'], 'lang_iso'=>$params['lang_iso']));
$submit = $this->CreateInputSubmit($id, 'submit', 'submit');
$return = $this->CreateLink($id, 'defaultadmin', $returnid, 'cancel',null,null,null,null,"class='pageback ui-state-default ui-corner-all'" );

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

?>
<h2>Add a new CommentSkeleton</h2>

<?php echo $error ?>
<?php echo $formStart; ?>
	<label for='text'>Text : </label>
	<textarea style='width: 20em;height: 6em;' name='<?php echo $id; ?>text'>Type a message here</textarea>
	
	<?php echo $submit; echo $return; ?>
</form>