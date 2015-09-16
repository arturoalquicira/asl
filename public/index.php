<?php
include 'phpQuery-onefile.php';

require '../vendor/autoload.php';
ORM::configure("mysql:host=localhost;dbname=asl1407");
ORM::configure("username", "root");
ORM::configure("password", "root");

ORM::configure('id_column_overrides',array(
'framework'=>'frameId',
'language'=>'langId',
'tutorial'=>'tutorId')
);

$app = new \Slim\Slim(array(
	'mode' => 'development',
	'debug' => true,
	'templates.path' => '../app/views/',
	'view' => new \Slim\Views\Twig()
));



//Home Page -- Listing of languages,frameworks, and tutorials
$app->get('/', function () use ($app) {
    session_start();
    if(isset($_SESSION['username_session'])){
    $loggedin = array('username' => $_SESSION['username_session']);

	$frameworks = ORM::for_table('framework')->inner_join('language', array('framework.language', '=', 'language.langId'))->find_many();
	$tutorials = ORM::for_table('tutorial')->inner_join('framework', array('tutorial.framework', '=', 'framework.frameId'))->inner_join('language', array('tutorial.language', '=', 'language.langId'))->find_many();
	$languages = ORM::for_table('language')->find_many();

	$app->render('index.html', array('frameworks' => $frameworks, 'tutorials' => $tutorials, 'languages' => $languages, 'loggedin'=> $loggedin));
    }else{
        $frameworks = ORM::for_table('framework')->inner_join('language', array('framework.language', '=', 'language.langId'))->find_many();
        $tutorials = ORM::for_table('tutorial')->inner_join('framework', array('tutorial.framework', '=', 'framework.frameId'))->inner_join('language', array('tutorial.language', '=', 'language.langId'))->find_many();
        $languages = ORM::for_table('language')->find_many();

        $app->render('index.html', array('frameworks' => $frameworks, 'tutorials' => $tutorials, 'languages' => $languages));

    }

});

//API Page -- Frameworks
$app->get('/frameworks/:id', function ($id) use ($app) {
	$framework = ORM::for_table('framework')->inner_join('language', array('framework.language', '=', 'language.langId'))->where('frameId', $id)->find_many();
	$framework_get = ORM::for_table('framework')->inner_join('language', array('framework.language', '=', 'language.langId'))->select_many("frameName", "langName")->where('frameId', $id)->find_many();

	foreach($framework_get as $framework_value){
		$framework_api = $framework_value->frameName;
		$language_api = $framework_value->langName;
	}


    $safe_framework_api = str_replace(' ', '%20', $framework_api);
    $safe_language_api= str_replace(' ', '%20', $language_api);

//	echo($language_api);
//	echo($framework_api);

	//ADD API HERE using two var above
//////////////////////////START STACKOVERFLOW API//////////////////////////
//CURL FUNCTION TO CURL THE RETURNED API OBJECT
    function curl($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, 'cURL');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING , "gzip");//<< Solution

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    $feed = curl('https://api.stackexchange.com/2.2/search?order=desc&sort=activity&intitle='.$framework_api.'%20'. $language_api.'&site=stackoverflow&key=Z*96yJDSjahJK9vjznMX4w((');

    //echo '<pre>';
    $feed = json_decode($feed,true);
    $api_array = $feed['items'];

    //var_dump($feed);

    //var_dump($api_array);
    $count_stackoverflow = count($feed['items']);
//////////////////////////END STACKOVERFLOW API//////////////////////////



//////////////////////////START YOUTUBE API//////////////////////////

    // //loads 10 first results in English ordered by relevance
    // $youtube_query=file_get_contents('https://gdata.youtube.com/feeds/api/videos?q='.$safe_framework_api.'+'.$safe_language_api.'&alt=json&key=AIzaSyA4dpUfRJ3Xzd08BrmObZKp6zIH60agWFE&max-results=10&category=programming&orderby=relevance_lang_en');
		//
    // //loads 10 more results starting from index 11
    // $youtube_query_loadMore=file_get_contents('https://gdata.youtube.com/feeds/api/videos?q='.$safe_framework_api.'+'.$safe_language_api.'&alt=json&key=AIzaSyA4dpUfRJ3Xzd08BrmObZKp6zIH60agWFE&max-results=10&start-index=11&category=programming&orderby=relevance_lang_en');
		//
    // $youtube_results = json_decode($youtube_query);
		//
    // $youtube_results_loadMore = json_decode($youtube_query_loadMore);
		//
    // ///echo '<pre>';
		//
    // //variable that holds the results parsed from the first query
    // $objects = $youtube_results->feed->entry;
		//
    // //variable that holds the results parsed from the second query
    // $objects_loadMore = $youtube_results_loadMore->feed->entry;
		//
    // //var_dump($objects_loadMore);
		//
    // //arrays to store the links created through the loops below
    // $youtube_data=array();
		//
    // $youtube_data_loadMore=array();
		//
		//
    // //loops through the object returned from the first query to the youtube API
    // $i=0;
    // foreach($objects as $object){
    //     //$j=$i+1;
    //     $t = get_object_vars($youtube_results->feed->entry[$i]);
    //     $y = $t["media\$group"];
    //     $youtube_data_link= array('link'=>$y->{"media\$content"}[0]->url);
		//
    //     if(count($youtube_data)==0){
    //         array_push($youtube_data, $youtube_data_link);
    //     } else {
    //         if (substr($youtube_data_link['link'], 0, 4) === 'http'){
    //             $youtube_data_link['link'] = $youtube_data_link['link'];
		//
    //             array_push($youtube_data, $youtube_data_link);
    //         }
		//
    //     }
		//
    //     $i++;
		//
    // }
    // //var_dump($youtube_data);
		//
    // //first query results count
    // $videos_count = count($youtube_data);
		//
		//
    // //loops through the object returned from the second query to the youtube API
    // $l=0;
    // foreach($objects_loadMore as $object_loadMore){
    //     //$j=$i+1;
    //     $a = get_object_vars($youtube_results_loadMore->feed->entry[$l]);
    //     $b = $a["media\$group"];
    //     //$youtube_data[$i] = array('title'=>$object->title->{"\$t"});
    //     $youtube_data_loadMore[$l] = array('link'=>$b->{"media\$content"}[0]->url);
    //     $l++;
    // }
    // //var_dump($youtube_data_loadMore);
		//
    // //second query results count
    // $load_more_count = count($youtube_data_loadMore);

//////////////////////////END YOUTUBE API//////////////////////////

//render page


//render page
    $app->render('template.html', array('framework'=>$framework,'stackoverflow_api'=>$api_array,'count_stackoverflow'=>$count_stackoverflow, 'videos'=>$youtube_data, 'load_more_videos'=>$youtube_data_loadMore, 'video_count'=> $videos_count, 'load_more_count'=> $load_more_count, 'language_doc'=>$language_api, 'framework_doc'=>$framework_api));
});






//API Page -- Tutorials
$app->get('/tutorials/:id', function ($id) use ($app) {
	$tutorial = ORM::for_table('tutorial')->inner_join('framework', array('tutorial.framework', '=', 'framework.frameId'))->inner_join('language', array('tutorial.language', '=', 'language.langId'))->where('tutorId', $id)->find_many();
	$tutorial_get = ORM::for_table('tutorial')->inner_join('framework', array('tutorial.framework', '=', 'framework.frameId'))->inner_join('language', array('tutorial.language', '=', 'language.langId'))->select_many("frameName","langName","tutorialName")->where('tutorId', $id)->find_many();

	foreach($tutorial_get as $tutorial_value){
		$language_api = $tutorial_value->langName;
		$framework_api = $tutorial_value->frameName;
		$tutorial_api = $tutorial_value->tutorialName;
	}


//	echo($language_api);
//	echo($framework_api);
//	echo($tutorial_api);

    $safe_tutorial_api= str_replace(' ', '%20', $tutorial_api);
    $safe_framework_api = str_replace(' ', '%20', $framework_api);
    $safe_language_api= str_replace(' ', '%20', $language_api);


    //////////////////////////START STACKOVERFLOW API//////////////////////////
//CURL FUNCTION TO CURL THE RETURNED API OBJECT
    function curl($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, 'cURL');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING , "gzip");//<< Solution

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

//QUERY TO STACKOVERFLOW API
    $feed = curl('https://api.stackexchange.com/2.2/search?order=desc&sort=activity&intitle='.$framework_api.'%20'. $language_api.'%20'.$tutorial_api.'&site=stackoverflow');

    //echo '<pre>';
    $feed = json_decode($feed,true);
    $api_array = $feed['items'];

    //print_r($feed['items']);
    //$count = count($feed['items']);
    //echo $count;

//////////////////////////END STACKOVERFLOW API//////////////////////////





//////////////////////////START YOUTUBE API//////////////////////////

    //loads 10 first results in English ordered by relevance
    $youtube_query=file_get_contents('https://gdata.youtube.com/feeds/api/videos?q='.$safe_framework_api.'+'.$safe_language_api.'+'.$safe_tutorial_api.'&alt=json&key=AIzaSyA4dpUfRJ3Xzd08BrmObZKp6zIH60agWFE&max-results=10&category=programming&orderby=relevance_lang_en');

    //loads 10 more results starting from index 11
    $youtube_query_loadMore=file_get_contents('https://gdata.youtube.com/feeds/api/videos?q='.$safe_framework_api.'+'.$safe_language_api.'+'.$safe_tutorial_api.'&alt=json&key=AIzaSyA4dpUfRJ3Xzd08BrmObZKp6zIH60agWFE&max-results=10&start-index=11&category=programming&orderby=relevance_lang_en');

    $youtube_results = json_decode($youtube_query);

    $youtube_results_loadMore = json_decode($youtube_query_loadMore);

    ///echo '<pre>';

    //variable that holds the results parsed from the first query
    $objects = $youtube_results->feed->entry;

    //variable that holds the results parsed from the second query
    $objects_loadMore = $youtube_results_loadMore->feed->entry;

    //var_dump($objects_loadMore);

    //arrays to store the links created through the loops below
    $youtube_data=array();

    $youtube_data_loadMore=array();


    //loops through the object returned from the first query to the youtube API
    $i=0;
    foreach($objects as $object){
        //$j=$i+1;
        $t = get_object_vars($youtube_results->feed->entry[$i]);
        $y = $t["media\$group"];
        //$youtube_data[$i] = array('title'=>$object->title->{"\$t"});
        $youtube_data[$i] = array('link'=>$y->{"media\$content"}[0]->url);
        $i++;

    }
    //var_dump($youtube_data);

    //first query results count
    $videos_count = count($youtube_data);


    //loops through the object returned from the second query to the youtube API
    $l=0;
    foreach($objects_loadMore as $object_loadMore){
        //$j=$i+1;
        $a = get_object_vars($youtube_results_loadMore->feed->entry[$l]);
        $b = $a["media\$group"];
        //$youtube_data[$i] = array('title'=>$object->title->{"\$t"});
        $youtube_data_loadMore[$l] = array('link'=>$b->{"media\$content"}[0]->url);
        $l++;
    }
    //var_dump($youtube_data_loadMore);

    //second query results count
    $load_more_count = count($youtube_data_loadMore);
    $count_stackoverflow = count($feed['items']);
//////////////////////////END YOUTUBE API//////////////////////////

    //////////////////////////START DOCUMENTATION//////////////////////////

    $language_doc = $language_api;

    if($language_doc=='PHP'){
        $language_url='http://php.net/docs.php';
        $framework_url= 'http://docs.slimframework.com/';
    } elseif($language_doc=='Python'){
        $language_url='https://www.python.org/doc/';
        $framework_url= 'http://flask.pocoo.org/';
    }elseif($language_doc=='javascript'){
        $language_url='https://developer.mozilla.org/en-US/docs/Web/JavaScript';
        $framework_url= 'http://nodejs.org/api/';
    }else{
        $language_url='https://www.ruby-lang.org/en/documentation/';
        $framework_url= 'http://rubyonrails.org/documentation/';
    }


//render page
    $app->render('template.html', array('tutorial'=>$tutorial,'stackoverflow_api'=>$api_array,'count_stackoverflow'=>$count_stackoverflow, 'videos'=>$youtube_data, 'load_more_videos'=>$youtube_data_loadMore, 'video_count'=> $videos_count, 'load_more_count'=> $load_more_count, 'language_url'=>$language_url, 'framework_url'=>$framework_url, 'language_doc'=>$language_api, 'framework_doc'=>$framework_api));

});


//API Page -- Languages
$app->get('/languages/:id', function ($id) use ($app) {
	$language = ORM::for_table('language')->where('langId', $id)->find_many();

	foreach($language as $language_value){
		$language_api = $language_value->langName;
	}

    $safe_language_api= str_replace(' ', '%20', $language_api);

//	echo($language_api);

    //////////////////////////START STACKOVERFLOW API//////////////////////////
//CURL FUNCTION TO CURL THE RETURNED API OBJECT
    function curl($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, 'cURL');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING , "gzip");//<< Solution

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

//QUERY TO STACKOVERFLOW API
    $feed = curl('https://api.stackexchange.com/2.2/search?order=desc&sort=activity&intitle='.$language_api.'&site=stackoverflow');

    //echo '<pre>';
    $feed = json_decode($feed,true);
    $api_array = $feed['items'];

    //print_r($feed['items']);
    //$count = count($feed['items']);
    //echo $count;
    $count_stackoverflow = count($feed['items']);
//////////////////////////END STACKOVERFLOW API//////////////////////////



//////////////////////////START YOUTUBE API//////////////////////////

    // //loads 10 first results in English ordered by relevance
    // $youtube_query=file_get_contents('https://gdata.youtube.com/feeds/api/videos?q='.$safe_language_api.'&alt=json&key=AIzaSyA4dpUfRJ3Xzd08BrmObZKp6zIH60agWFE&max-results=10&category=programming&orderby=relevance_lang_en');
		//
    // //loads 10 more results starting from index 11
    // $youtube_query_loadMore=file_get_contents('https://gdata.youtube.com/feeds/api/videos?q='.$safe_language_api.'&alt=json&key=AIzaSyA4dpUfRJ3Xzd08BrmObZKp6zIH60agWFE&max-results=10&start-index=11&category=programming&orderby=relevance_lang_en');
		//
    // $youtube_results = json_decode($youtube_query);
		//
    // $youtube_results_loadMore = json_decode($youtube_query_loadMore);
		//
    // ///echo '<pre>';
		//
    // //variable that holds the results parsed from the first query
    // $objects = $youtube_results->feed->entry;
		//
    // //variable that holds the results parsed from the second query
    // $objects_loadMore = $youtube_results_loadMore->feed->entry;
		//
    // //var_dump($objects_loadMore);
		//
    // //arrays to store the links created through the loops below
    // $youtube_data=array();
		//
    // $youtube_data_loadMore=array();
		//
		//
    // //loops through the object returned from the first query to the youtube API
    // $i=0;
    // foreach($objects as $object){
    //     //$j=$i+1;
    //     $t = get_object_vars($youtube_results->feed->entry[$i]);
    //     $y = $t["media\$group"];
    //     //$youtube_data[$i] = array('title'=>$object->title->{"\$t"});
    //     $youtube_data[$i] = array('link'=>$y->{"media\$content"}[0]->url);
    //     $i++;
		//
    // }
    // //var_dump($youtube_data);
		//
    // //first query results count
    // $videos_count = count($youtube_data);
		//
		//
    // //loops through the object returned from the second query to the youtube API
    // $l=0;
    // foreach($objects_loadMore as $object_loadMore){
    //     //$j=$i+1;
    //     $a = get_object_vars($youtube_results_loadMore->feed->entry[$l]);
    //     $b = $a["media\$group"];
    //     //$youtube_data[$i] = array('title'=>$object->title->{"\$t"});
    //     $youtube_data_loadMore[$l] = array('link'=>$b->{"media\$content"}[0]->url);
    //     $l++;
    // }
    // //var_dump($youtube_data_loadMore);
		//
    // //second query results count
    // $load_more_count = count($youtube_data_loadMore);

//////////////////////////END YOUTUBE API//////////////////////////

    //////////////////////////START DOCUMENTATION//////////////////////////

    $language_doc = $language_api;

    if($language_doc=='PHP'){
        $language_url='http://php.net/docs.php';
    } elseif($language_doc=='Python'){
        $language_url='https://www.python.org/doc/';
    }elseif($language_doc=='Javascript'){
        $language_url='https://developer.mozilla.org/en-US/docs/Web/JavaScript';
    }else{
        $language_url='https://www.ruby-lang.org/en/documentation/';
    }

    ///////////////////////////// Code Snippets ////////////////////
//$test=$language_api;


//echo $test;

//    if($test==='PHP'){
//        $file = 'http://www2.latech.edu/~acm/helloworld/PHP.html'; // see below for source
//        phpQuery::newDocumentFileHTML($file);
//        $titleElement = pq('pre:contains("echo("Hello, World!")")');
//    }elseif($test==='Javascript'){
//        $file = 'http://www2.latech.edu/~acm/helloworld/javascript.html'; // see below for source
//        phpQuery::newDocumentFileHTML($file);
//        $titleElement_array = pq('pre');
//        $titleElement_string = explode(" ", $titleElement_array);
//        $titleElement_string_concat=$titleElement_string[7].$titleElement_string[8].$titleElement_string[9];
//       // echo $titleElement_string_concat;
//        $titleElement_string_concat_sub = explode("&amp", $titleElement_string_concat);
//        //echo  $titleElement_string_concat_sub[0];
//        $titleElement = $titleElement_string_concat_sub[0];
//        echo $titleElement;
//    }elseif($test==='Python'){
//        $file = 'http://www2.latech.edu/~acm/helloworld/python.html'; // see below for source
//        phpQuery::newDocumentFileHTML($file);
//        $titleElement = pq('pre:contains("print "Hello world!")');
//    }else{
//        $file = 'http://www2.latech.edu/~acm/helloworld/ruby.htm'; // see below for source
//        phpQuery::newDocumentFileHTML($file);
//        $titleElement = pq('pre:contains("puts "Hello World!")');
//    }
//
//   $title = htmlspecialchars_decode($titleElement->html());

// And output the result
//  echo '<pre> Title: ' .  htmlentities($title) . '</pre>';


//render page
    $app->render('template.html', array('language'=>$language,'stackoverflow_api'=>$api_array,'count_stackoverflow'=>$count_stackoverflow, 'language_url'=>$language_url, 'language_doc'=>$language_doc));
});

//=========================END API ROUTES====================================




// logout route
$app->get('/logout', function () use ($app) {
session_start();
session_destroy();
$app->redirect('/');
});

//////////////////// LOG IN ////////////////////////

$app->post('/login', function () use ($app) {

session_start();

$same_username= $_POST['username'];
$query_username= ORM::for_table('user')->select('username')->where('username', $same_username)->find_one();

$password = $_POST['password'];
$pass= $app->request->post($password);
/*
$salt= 'CreativeFramworksSalt';
$pwd = md5($pass.$salt);
*/
$pwd = $pass;
$query_password= ORM::for_table('user')->select('password')->where('username', $same_username)->find_one();


if(!isset($_POST['username']) or !isset($_POST['password']))
{
// $message = 'Please enter a valid username and password';
// $app->render('login-error.html', array('message'=>$message));
$app->flash('loginerror','Please enter a valid username and password');
$app->redirect('/');

}
/*** check the username is the correct length ***/
elseif (strlen( $_POST['username']) > 20 || strlen($_POST['username']) < 4)
{
// $message = 'Incorrect Length for Username';
// $app->render('login-error.html', array('message'=>$message));
$app->flash('loginerror','Incorrect Length for Username');
$app->redirect('/');

}
/*** check the password is the correct length ***/
elseif (strlen( $_POST['password']) > 20 || strlen($_POST['password']) < 4)
{
// $message = 'Incorrect Length for Password';
// $app->render('login-error.html', array('message'=>$message));
$app->flash('loginerror','Incorrect Length for Password');
$app->redirect('/');

}
/*** check the username has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['username']) != true)
{
/*** if there is no match ***/
// $message = "Username must be alpha numeric";
// $app->render('login-error.html', array('message'=>$message));
$app->flash('loginerror','Username must be alpha numeric');
$app->redirect('/');

}
/*** check the password has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['password']) != true)
{
/*** if there is no match ***/
// $message = "Password must be alpha numeric";
// $app->render('login-error.html', array('message'=>$message));
$app->flash('loginerror','Password must be alpha numeric');
$app->redirect('/');

}
elseif ($query_username == false)
{
/*** if there is NO match ***/
// $message = "User doesn't exist";
// $app->render('login-error.html', array('message'=>$message));
$app->flash('loginerror','User doesn\'t exist');
$app->redirect('/');

}
elseif ($query_password->get('password') !== $pwd)
{
/*** if password does NOT match ***/
// $message = "Incorrect Password";
// $app->render('login-error.html', array('message'=>$message));
$app->flash('loginerror','Incorrect Password');
$app->redirect('/');

}
else
{
/*** Save userID and userName into $_SESSION  ***/

$query_data= ORM::for_table('user')->where('username', $same_username)->find_one();
$save_userid= $query_data['userid'];
$save_username= $query_data['username'];

$_SESSION['userid_session']= $save_userid;
$_SESSION['username_session']= $save_username;

/******** Redirect to the LANDING PAGE ********/

$app->redirect('/dashboard');
};
});

//========================= DASHBOARD ====================================

//RENDER the dashboard
$app->get('/dashboard', function () use ($app) {
    session_start();
    if(isset($_SESSION['username_session'])){

        $languages = ORM::for_table('language')->find_many();
        $frameworks = ORM::for_table('framework')->inner_join('language', array('framework.language', '=', 'language.langId'))->find_many();
        $tutorials = ORM::for_table('tutorial')->inner_join('framework', array('tutorial.framework', '=', 'framework.frameId'))->inner_join('language', array('tutorial.language', '=', 'language.langId'))->find_many();

        $app->render('dashboard.html',array('languages' => $languages, 'frameworks' => $frameworks, 'tutorials'=> $tutorials));
    }
    else{
        $app->redirect('/');
    }
});

//-------------LANGUAGE TABLE---------------
//ADD new language
$app->post('/add_language', function () use ($app) {
    $new = ORM::for_table('language')->create();
    $new->langName = $app->request->post('language');
    $new->langNote = "No instructor notes exist";
    $new->save();
    $app->redirect('/dashboard');
});
//DELETE language
$app->get('/delete_language/:id', function ($id) use ($app){
    $delete = ORM::for_table('language')->where('langId', $id)->find_one();
    $also_delete = ORM::for_table('framework')->where('language', $id)->find_one();
    $then_delete = ORM::for_table('tutorial')->where('language', $id)->find_one();

    $delete->delete();
    if($also_delete){
        $also_delete->delete();
    }
    if($then_delete){
        $then_delete->delete();
    }

    $app->redirect('/dashboard');
});
//EDIT language
$app->get('/edit_language/:id', function ($id) use ($app) {
    $edit = ORM::for_table('language')->where('langId', $id)->find_many();
    $app->render('edit.html',array('editlang' => $edit));
});
$app->put('/edit_language/:id', function ($id) use ($app) {
    $post_edit = ORM::for_table('language')->where('langId', $id)->find_one();
    $post_edit->langName = $app->request->put('language');
    $post_edit->save();
    $app->redirect('/dashboard');
});
//NOTE for language
$app->get('/lang_note/:id', function ($id) use ($app) {
    $lang_note = ORM::for_table('language')->where('langId', $id)->find_many();
    $app->render('note.html',array('langnote' => $lang_note));
});
$app->put('/lang_note/:id', function ($id) use ($app) {
    $post_edit = ORM::for_table('language')->where('langId', $id)->find_one();
    $post_edit->langNote = $app->request->put('note');
    $post_edit->save();
    $app->redirect('/dashboard');
});
//EDIT link
$app->get('/edit_link/:id', function ($id) use ($app) {
    $edit = ORM::for_table('language')->where('langId', $id)->find_many();
    $app->render('edit.html',array('editlink' => $edit));
});
$app->put('/edit_link/:id', function ($id) use ($app) {
    $post_edit = ORM::for_table('language')->where('langId', $id)->find_one();
    $post_edit->langLink = $app->request->put('link');
    $post_edit->save();
    $app->redirect('/dashboard');
});


//-------------FRAMEWORK TABLE---------------
//ADD new framework
$app->post('/add_framework', function () use ($app) {
    $new = ORM::for_table('framework')->create();
    $new->language = $app->request->post('language');
    $new->frameName = $app->request->post('frame_name');
    $new->frameNote = "No instructor notes exist";
    $new->save();
    $app->redirect('/dashboard');
});
//DELETE framework
$app->get('/delete_framework/:id', function ($id) use ($app){
    $delete = ORM::for_table('framework')->where('frameId', $id)->find_one();
    $also_delete = ORM::for_table('tutorial')->where('framework', $id)->find_one();

    $delete->delete();
    if($also_delete){
        $also_delete->delete();
    }
    $app->redirect('/dashboard');
});
//EDIT framework
$app->get('/edit_framework/:id', function ($id) use ($app) {
    $edit = ORM::for_table('framework')->where('frameId', $id)->find_many();
    $edit_lang = ORM::for_table('language')->find_many();
    $app->render('edit.html', array('editframe' => $edit, 'lang' => $edit_lang));
});
$app->put('/edit_framework/:id', function ($id) use ($app) {
    $post_edit = ORM::for_table('framework')->where('frameId', $id)->find_one();
    $post_edit->language = $app->request->put('language');
    $post_edit->frameName = $app->request->put('framework');
    $post_edit->save();
    $app->redirect('/dashboard');
});
//NOTE for framework
$app->get('/frame_note/:id', function ($id) use ($app) {
    $frame_note = ORM::for_table('framework')->where('frameId', $id)->find_many();
    $app->render('note.html',array('framenote' => $frame_note));
});
$app->put('/frame_note/:id', function ($id) use ($app) {
    $post_edit = ORM::for_table('framework')->where('frameId', $id)->find_one();
    $post_edit->frameNote = $app->request->put('note');
    $post_edit->save();
    $app->redirect('/dashboard');
});
//EDIT link
$app->get('/edit_frame_link/:id', function ($id) use ($app) {
    $edit_frame_link = ORM::for_table('framework')->where('frameId', $id)->find_many();
    $app->render('edit.html',array('editframelink' => $edit_frame_link));
});
$app->put('/edit_frame_link/:id', function ($id) use ($app) {
    $post_edit = ORM::for_table('framework')->where('frameId', $id)->find_one();
    $post_edit->frameLink = $app->request->put('link');
    $post_edit->save();
    $app->redirect('/dashboard');
});

//-------------TUTORIAL TABLE---------------
//ADD new tutorial
$app->post('/add_tutorial', function () use ($app) {
    $new = ORM::for_table('tutorial')->create();
    $new->language = $app->request->post('language');
    $new->framework = $app->request->post('frame_name');
    $new->tutorialName = $app->request->post('tutorial_name');
    $new->tutorNote = "No instructor notes exist";
    $new->save();
    $app->redirect('/dashboard');
});
//DELETE tutorial
$app->get('/delete_tutorial/:id', function ($id) use ($app){
    $delete = ORM::for_table('tutorial')->where('tutorId', $id)->find_one();
    $delete->delete();
    $app->redirect('/dashboard');
});
//EDIT tutorial
$app->get('/edit_tutorial/:id', function ($id) use ($app) {
    $edit = ORM::for_table('tutorial')->where('tutorId', $id)->find_many();
    $edit_lang = ORM::for_table('language')->find_many();
    $edit_frame = ORM::for_table('framework')->find_many();
    $app->render('edit.html', array('edittutor' => $edit, 'frame' => $edit_frame, 'lang' => $edit_lang));
});
$app->put('/edit_tutorial/:id', function ($id) use ($app) {
    $post_edit = ORM::for_table('tutorial')->where('tutorId', $id)->find_one();
    $post_edit->language = $app->request->put('language');
    $post_edit->framework = $app->request->put('framework');
    $post_edit->tutorialName = $app->request->put('tutorial');
    $post_edit->save();
    $app->redirect('/dashboard');
});
//NOTE for tutorial
$app->get('/tutor_note/:id', function ($id) use ($app) {
    $tutor_note = ORM::for_table('tutorial')->where('tutorId', $id)->find_many();
    $app->render('note.html',array('tutornote' => $tutor_note));
});
$app->put('/tutor_note/:id', function ($id) use ($app) {
    $post_edit = ORM::for_table('tutorial')->where('tutorId', $id)->find_one();
    $post_edit->tutorNote = $app->request->put('note');
    $post_edit->save();
    $app->redirect('/dashboard');
});



$app->run();
