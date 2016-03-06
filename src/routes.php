<?php
srcloader('m_account.php');
// Routes

//auth
$app->post('/nbcuvuyswdkajdh', function ($req,$res) {
	$account = new Accountmodel;
	$email = @$_POST['email'];
	$password = sha1(@$_POST['password']);
	$auth = $account->auth($email,$password);
	return tojson($res,$auth);
});

//register
$app->post('/aukbykawjdbfyt', function ($req,$res) {
	$account = new Accountmodel;
	$data['email'] = @$_POST['email'];
	$data['password'] = sha1(@$_POST['password']);
	$data['fullname'] = @$_POST['fullname'];
	$hasil = $account->register($data);
	return tojson($res,$hasil);
});

//update location
$app->post('/vbyuvbklsjdhqwidu', function($req, $res, $args){
	$account = new Accountmodel;
	$token = $account->authToken();

	if ($token['status']) {
		$hasil['status'] = $account->update_location($_POST['longitude'],$_POST['latitude'], $_GET['user_id']);	
	}else{
		$hasil = $token;
	}

	return tojson($res,$hasil);
});

//authtoken
$app->post('/kshvbasdualjdahu', function ($req,$res) {
	$account = new Accountmodel;
	return tojson($res, $account->authToken());
});

//logout
$app->get('/bcxgyuaewsdfygdh',function($req,$res,$args){
	$account = new Accountmodel;
	$token = $account->authToken();
	if ($token['status']) {
		$hasil['status'] = $account->logout((int) $_GET['user_id']);
		$hasil['message'] = 'logout success';
	}else{
		$hasil = $token;
	}
	return tojson($res,$hasil);
});

//get mybooks and wishlist
$app->get('/xmzcvbehduahyd/{type}',function($req,$res,$args){
	srcloader('m_books.php');
	$account = new Accountmodel;
	$buku = new Booksmodel;

	$token = $account->authToken();
	$user_id = (int) @$_GET['user_id'];
	$page = (int) $args['page'];

	$type = ($args['type']==1) ? 1 : 2 ;
	if ((string)$token['status']) {
		$hasil = $buku->getBooks($user_id, $type, $page);
	}else{
		$token['status'] = 3;
		$hasil = $token;
	}

	return tojson($res,$hasil);
});

//post mybooks and wishlist
$app->post('/xmzcvbehduahyd/{type}',function($req,$res,$args){
	srcloader('m_books.php');
	$account = new Accountmodel;
	$buku = new Booksmodel;

	$token = $account->authToken();
	$user_id = (int) @$_GET['user_id'];
	$page = (int) $args['page'];

	$type = ($args['type']==1) ? 1 : 2 ;
	if ((string)$token['status']) {
		$hasil = $buku->getBooks($user_id, $type, $page);
	}else{
		$token['status'] = 3;
		$hasil = $token;
	}

	return tojson($res,$hasil);
});

//Search books by isbn or query
$app->get('/mmahdauywgdsh/{type}/{query}',function($req,$res,$args){
	srcloader('m_books.php');
	$buku = new Booksmodel;
	$account = new Accountmodel;
	$token = $account->authToken();

	if ($args['type']=='isbn') {
		$query = (int)$args['query'];
		$isbn = true;
	}else{
		$query = (string)$args['query'];
		$isbn = false;
	}

	if ((string)$token['status']) {
		$hasil = $buku->search($query,$isbn);
	}else{
		$token['status'] = 3;
		$hasil = $token;
	}
	
	return tojson($res,$hasil);
});

//search available books
$app->get('/qppwcfmiqwfuqy/{query}/{page}', function ($req,$res,$args) {
	srcloader('m_books.php');
	$account = new Accountmodel;
	$buku = new Booksmodel;

	$token = $account->authToken();
	if ($token['status']) {
		$hasil = $buku->search_books((int)$_GET['user_id'], $args['query']);
	}else{
		$hasil = $token;
	}
	return tojson($res,$hasil);
});