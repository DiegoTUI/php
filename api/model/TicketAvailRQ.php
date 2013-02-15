<?php
/**
 * MoveinBlue API.
 * Activity-related functions and globals.
 * (C) 2011 MoveinBlue.
 */

include_once 'inc/web_entity.php';
include_once 'inc/comment.php';
include_once 'inc/request.php';

/**
 * A rating for an activity. Store user id and numeric rating, return count and average.
 */
$ACTIVITY_RATING_ENTITY = new WebEntity('activity rating', 'ratings', array(
	new PrimaryKey('user_id'),
	new Mandatory('rating', null, $NUMBER),
));

/**
 * A like for an activity. Store just user id, return counte.
 */
$LIKE_ACTIVITY_ENTITY = new WebEntity('activity likes', 'likes', array(
	new PrimaryKey('user_id'),
));

/**
 * A plan in an activity. Only store id and name.
 */
$PLAN_IN_ACTIVITY_ENTITY = new WebEntity('plan in activity', 'plans', array(
	new PrimaryKey('plan_id'),
	new Mandatory('name'),
));

/**
 * Used to choose the right collection for activities depending on the source attribute.
 */
$activity_collections = array(
	'mib' => 'activities',
	'wikitravel' => 'wikitravel_activities',
	'entradas.com' => 'entradas_activities',
);

/**
 * MoveinBlue activity: something to do while on holidays.
 */
$ACTIVITY_ENTITY = new WebEntity('activity', 'activities', array(
	new PrimaryKey('activity_id'),
	new Mandatory('name', null, $TRANSLATED),
	new Mandatory('category'),
	new Optional('description', null, $TRANSLATED),
	new Optional('tags', null, $COMMA_SEPARATED_TRANSLATED),
	new Optional('image_url', 'urls.image'),
	new Optional('image_attribution_url', 'urls.image_attribution'),
	new Optional('web_page', 'urls.official'),
	new Optional('wikipedia_url', 'urls.wikipedia'),
	new Optional('tripadvisor_url', 'urls.tripadvisor'),
	new CollectionChooser('source', $activity_collections),
	new Optional('attribution_url', 'urls.attribution'),
	new Optional('link'),
	new Optional('duration', null, $NUMBER),
	new Optional('price', 'admission.price', $NUMBER),
	new Optional('min_price', 'admission.min_price', $NUMBER),
	new Optional('reservation_phone', 'admission.reservation_phone'),
	new Optional('reservation_webpage', 'admission.reservation_webpage'),
	new Optional('reservation_email', 'admission.reservation_email'),
	new Optional('admission_notes', 'admission.notes', $TRANSLATED),
	new Optional('admission_info'),
	new Optional('rating', null, $NUMBER),
	new AveragedList('ratings', 'rating', $ACTIVITY_RATING_ENTITY),
	new CountedList('likes', $LIKE_ACTIVITY_ENTITY),
	new Optional('address', 'position.address'),
	new Optional('latitude', 'position.latitude', $NUMBER),
	new Optional('longitude', 'position.longitude', $NUMBER),
	new Optional('destination'),
	new Optional('distance', null, $NUMBER),
	new Optional('from_time', 'opening[0].from_time'),
	new Optional('to_time', 'opening[0].to_time'),
	new Optional('weekday_monday', 'opening[0].monday'),
	new Optional('weekday_tuesday', 'opening[0].tuesday'),
	new Optional('weekday_wednesday', 'opening[0].wednesday'),
	new Optional('weekday_thursday', 'opening[0].thursday'),
	new Optional('weekday_friday', 'opening[0].friday'),
	new Optional('weekday_saturday', 'opening[0].saturday'),
	new Optional('weekday_sunday', 'opening[0].sunday'),
	new Optional('season_from', 'opening[0].season_from'),
	new Optional('season_to', 'opening[0].season_to'),
	new Optional('from_time_2', 'opening[1].from_time'),
	new Optional('to_time_2', 'opening[1].to_time'),
	new Optional('weekday_monday_2', 'opening[1].monday'),
	new Optional('weekday_tuesday_2', 'opening[1].tuesday'),
	new Optional('weekday_wednesday_2', 'opening[1].wednesday'),
	new Optional('weekday_thursday_2', 'opening[1].thursday'),
	new Optional('weekday_friday_2', 'opening[1].friday'),
	new Optional('weekday_saturday_2', 'opening[1].saturday'),
	new Optional('weekday_sunday_2', 'opening[1].sunday'),
	new Optional('season_from_2', 'opening[1].season_from'),
	new Optional('season_to_2', 'opening[1].season_to'),
	new Optional('season_notes', null, $TRANSLATED),
	new Optional('opening_info'),
	new Optional('opening_info_full'),
	new Optional('service_1', null, $TRANSLATED),
	new Optional('service_2', null, $TRANSLATED),
	new Optional('service_3', null, $TRANSLATED),
	new Optional('must_do', null, $BOOLEAN),
	new Optional('hidden', null, $BOOLEAN),
	new Optional('notes'),
	new Optional('author', 'editorial.author'),
	new Optional('reviewer', 'editorial.reviewer'),
	new Optional('reviewed', 'editorial.reviewed'),
	new Optional('reviewer_notes', 'editorial.reviewer_notes'),
	new Optional('translator', 'editorial.translator'),
	new Optional('translated', 'editorial.translated'),
	new Optional('translator_notes', 'editorial.translator_notes'),
	new CountedList('comments', $COMMENT_ENTITY),
	new CountedList('plans', $PLAN_IN_ACTIVITY_ENTITY),
	new Visible('status'),
	new Visible('created_user_id', 'created.user.user_id'),
	new Visible('created_username', 'created.user.name'),
	new Visible('created_time', 'created.timestamp', $DATE),
	new Visible('last_modified_user_id', 'last_modified.user.user_id'),
	new Visible('last_modified_username', 'last_modified.user.name'),
	new Visible('last_modified_time', 'last_modified.timestamp', $DATE),
));
$ACTIVITY_ENTITY->add_surrogates(array(
	new Optional('image_url'),
	new Optional('web_page'),
	new Optional('wikipedia_url'),
	new Optional('tripadvisor_url'),
	new Optional('address'),
	new Optional('latitude'),
	new Optional('longitude'),
	new Optional('from_time'),
	new Optional('to_time'),
	new Optional('weekday_monday'),
	new Optional('weekday_tuesday'),
	new Optional('weekday_wednesday'),
	new Optional('weekday_thursday'),
	new Optional('weekday_friday'),
	new Optional('weekday_saturday'),
	new Optional('weekday_sunday'),
	new Optional('season_from'),
	new Optional('season_to'),
	new Optional('price'),
	new Optional('min_price'),
	new Optional('reservation_phone'),
	new Optional('reservation_webpage'),
	new Optional('reservation_email'),
	new Optional('admission_notes'),
	new Optional('author'),
	new Optional('reviewer'),
	new Optional('reviewed'),
	new Optional('reviewer_notes'),
	new Visible('created_user_id', 'created.user_id'),
	new Visible('created_username', 'created.username'),
	new Visible('created_username', 'created.user.username'),
	new Visible('created_time', 'created.timestamp.iso_time'),
	new Visible('last_modified_user_id', 'last_modified.user_id'),
	new Visible('last_modified_username', 'last_modified.username'),
	new Visible('last_modified_username', 'last_modified.user.username'),
	new Visible('last_modified_time', 'last_modified.timestamp.iso_time'),
));
$ACTIVITY_ENTITY->set_indexes(array('name', 'link'));
$ACTIVITY_ENTITY->set_postprocess('add_destination');

/**
 * MoveinBlue activity, formatted for display.
 */
$DISPLAY_ACTIVITY_ENTITY = new WebEntity('activity', 'activities', array(
	new PrimaryKey('activity_id'),
	new Mandatory('name', null, $TRANSLATED_ESCAPED),
	new Optional('categories'),
	new Optional('description', null, $TRANSLATED_ESCAPED),
	new Optional('tags', null, $COMMA_SEPARATED_TRANSLATED),
	new Optional('image_url', 'urls.image'),
	new Optional('image_attribution_url', 'urls.image_attribution'),
	new Optional('web_page', 'urls.official'),
	new Optional('wikipedia_url', 'urls.wikipedia'),
	new Optional('tripadvisor_url', 'urls.tripadvisor'),
	new CollectionChooser('source', $activity_collections),
	new Optional('attribution_url', 'urls.attribution'),
	new Optional('link'),
	new Optional('duration', null, $NUMBER),
	new Optional('price', 'admission.price', $NUMBER),
	new Optional('min_price', 'admission.min_price', $NUMBER),
	new Optional('reservation_phone', 'admission.reservation_phone'),
	new Optional('reservation_webpage', 'admission.reservation_webpage'),
	new Optional('reservation_email', 'admission.reservation_email'),
	new Optional('admission_notes', 'admission.notes', $TRANSLATED_ESCAPED),
	new Optional('admission_info'),
	new Optional('rating', null, $NUMBER),
	new AveragedList('ratings', 'rating', $ACTIVITY_RATING_ENTITY),
	new CountedList('likes', $LIKE_ACTIVITY_ENTITY),
	new Optional('address', 'position.address'),
	new Optional('latitude', 'position.latitude', $NUMBER),
	new Optional('longitude', 'position.longitude', $NUMBER),
	new Optional('destination', null, $ESCAPED),
	new Optional('distance', null, $NUMBER),
	new Optional('season_notes', null, $TRANSLATED_ESCAPED),
	new Optional('opening_info'),
	new Optional('opening_info_full'),
	new Optional('service_1', null, $TRANSLATED_ESCAPED),
	new Optional('service_2', null, $TRANSLATED_ESCAPED),
	new Optional('service_3', null, $TRANSLATED_ESCAPED),
	new Optional('must_do', null, $BOOLEAN),
	new Optional('hidden', null, $BOOLEAN),
	new Visible('created_user_id', 'created.user.user_id'),
	new Visible('created_username', 'created.user.name'),
	new Visible('created_time', 'created.timestamp', $DATE),
	new CountedList('comments', $COMMENT_ENTITY),
	new CountedList('plans', $PLAN_IN_ACTIVITY_ENTITY),
));
$DISPLAY_ACTIVITY_ENTITY->set_indexes(array('name', 'link'));

/**
 * Shortened activity, to return in searches.
 */
$ACTIVITY_SEARCH_ENTITY = new WebEntity('activity', 'activities', array(
	new Mandatory('activity_id'),
	new Mandatory('name', null, $TRANSLATED_ESCAPED),
	new Optional('category'),
	new Optional('tags', null, $COMMA_SEPARATED_TRANSLATED),
	new CollectionChooser('source', $activity_collections),
	new Optional('admission_info'),
	new Optional('rating', null, $NUMBER),
	new AveragedList('ratings', 'rating', $ACTIVITY_RATING_ENTITY),
	new Optional('latitude', 'position.latitude', $NUMBER),
	new Optional('longitude', 'position.longitude', $NUMBER),
	new Optional('destination', null, $ESCAPED),
	new Optional('distance', null, $NUMBER),
	new Optional('opening_info'),
	new Optional('opening_info_full'),
	new Optional('must_do', null, $BOOLEAN),
	new Optional('hidden', null, $BOOLEAN),
	new Optional('link'),
));

/**
 * A custom activity created by users.
 */
$PRIVATE_ACTIVITY_ENTITY = new WebEntity('private activity', 'private_activities', array(
	new PrimaryKey('activity_id'),
	new Mandatory('name', null, $TRANSLATED_ESCAPED),
	new Optional('description', null, $TRANSLATED_ESCAPED),
	new Optional('latitude', 'position.latitude', $NUMBER),
	new Optional('longitude', 'position.longitude', $NUMBER),
	new Optional('destination', null, $ESCAPED),
	new Optional('distance', null, $NUMBER),
	new Optional('duration', null, $NUMBER),
	new Optional('source'),
	new Optional('plan_id'),
));
$PRIVATE_ACTIVITY_ENTITY->set_postprocess('add_destination');
