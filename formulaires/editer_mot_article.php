<?php

include_spip('inc/autoriser');

function formulaires_editer_mot_article_charger_dist($id_article='new', $id_groupe='', $retour=''){
	
	$id_mot = sql_getfetsel('mot.id_mot','spip_mots as mot left join spip_mots_articles as mots_articles ON (mot.id_mot=mots_articles.id_mot)','mots_articles.id_article='.intval($id_article).' AND mot.id_groupe='.intval($id_groupe));
	
	$valeurs['id_article'] = $id_article;
	$valeurs['id_groupe'] = $id_groupe;
	$valeurs['id_mot'] = $id_mot;
	$valeurs['editable'] = true;
	
	if (!autoriser('modifier', 'article', $id_article))
		$valeurs['editable'] = false;

	return $valeurs;
}

function formulaires_editer_mot_article_verifier_dist($id_article='new', $id_groupe='', $retour=''){
	$erreurs = array();
	return $erreurs;
}

function formulaires_editer_mot_article_traiter_dist($id_article='new', $id_groupe='', $retour=''){
	
	$message = array('editable'=>true, 'message_ok'=>'');
	
	$id_mot_ancien = sql_getfetsel('mot.id_mot','spip_mots as mot left join spip_mots_articles as mots_articles ON (mot.id_mot=mots_articles.id_mot)','mots_articles.id_article='.intval($id_article).' AND mot.id_groupe='.intval($id_groupe));
	
	// si aucun mot selectionne on delie le mot de ce groupe
	if(!$id_mot = _request('id_mot')){
		sql_delete('spip_mots_articles','id_article='.intval($id_article).' AND id_mot='.intval($id_mot_ancien));
	} else {
		if ($id_mot_ancien != $id_mot) {
			// on delie l'ancien mot
			sql_delete('spip_mots_articles','id_article='.intval($id_article).' AND id_mot='.intval($id_mot_ancien));
			// on lie le nouveau
			sql_insertq('spip_mots_articles', array('id_mot' =>$id_mot,  'id_article' => $id_article));
			// on invalide le cache
		}
	}
	
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_article/$id_article'");
	
	if ($retour) {
		include_spip('inc/headers');
		$message .= redirige_formulaire($retour);
	}
	
	return $message;
	
}

?>
