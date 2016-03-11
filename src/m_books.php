<?php

class Booksmodel{
	function search($query,$isbn){
		$url = "https://www.goodreads.com/search/index.xml?key=vsEIaGrx3fpSq4YRIiPcg&q=".urlencode($query);
		// $url ="http://localhost/sebuku/src/dummy.xml";
		$xml = simplexml_load_string(file_get_contents($url));
		$hasil['total_results'] = (int)$xml->search->{'results-end'};
		$hasil['query'] = (string)$xml->search->query;
		if ($hasil['total_results'] != 0) {
			for ($i=0; $i <=$hasil['total_results']-1 ; $i++) {
				$data['title'] = (string)$xml->search->results->work[$i]->best_book->title;
				$data['author'] = (string)$xml->search->results->work[$i]->best_book->author->name;
				$data['year'] = (int)$xml->search->results->work[$i]->original_publication_year;
				$data['image_url'] = (string)$xml->search->results->work[$i]->best_book->image_url;
				$hasil['result'][] = $data;
				if ($isbn) {
					$data['isbn'] = $hasil['query'];
					$this->save_books($data);
				}
			}
			$hasil['status'] = (bool) 1;
		}else{
			$hasil['total_results'] = (int) 0;
			$hasil['status'] = (bool) 0;
			$hasil['message'] = "Books not found";
		}
		return $hasil;
	}

	function save_books($data){
		$db = connect_db();
		$query = $db->query("SELECT * FROM books where books.isbn='$data[isbn]' ");
		if ($query->num_rows == 0) {
			$save = $db->query("INSERT INTO books(isbn, title, author, year, img_url) 
									VALUES ('$data[isbn]','$data[title]','$data[author]','$data[year]','$data[image_url]') ");
			if ($save) {
				$hasil = true;
			}else{
				$hasil = false;
			}
		}else{
			$hasil = false;
		}
		return $hasil;
	}

	function search_books($user_id, $query, $page=0){
		$db = connect_db();
		$search_exploded = explode (" ", $query); 
		$construct = "";
		foreach( $search_exploded as $search_each) {
             	$construct .="AND (title LIKE '%$search_each%' OR author LIKE '%$search_each%')  ";
		}
		$location = $this->get_location($user_id);
		$lat = $location['latitude'];
		$long = $location['longitude'];

		$result = $db->query("SELECT title, mybooks.isbn, author, fullname, mybooks.longitude, mybooks.latitude, 
								mybooks_id, user.user_id, img_url, 111045
										* DEGREES(ACOS(COS(RADIANS('$lat'))
							                 	* COS(RADIANS(mybooks.latitude))
							                 	* COS(RADIANS('$long') - RADIANS(mybooks.longitude))
							                 	+ SIN(RADIANS('$lat'))
							                 	* SIN(RADIANS(mybooks.latitude)))) AS distance
								FROM mybooks, books, user 
								WHERE mybooks.isbn = books.isbn 
									AND mybooks.user_id = user.user_id
									AND mybooks.type = 1
									AND mybooks.status = 1
									AND user.user_id != '$user_id '".$construct."
								ORDER BY distance
								LIMIT $page , 5
									");

		$res['sum'] = $result->num_rows;

		if ($res['sum'] >= 1) {
			while ($res['data'][] = $result->fetch_assoc()) {
			}
			$res['status'] = true;
	           	$res['message'] = "Found";
	        }else{
	            $res['status'] = false;
	            $res['message'] = "Not found";
	        }

	        return $res;
	}

	function get_location($user_id){
		$db = connect_db();
		$result = $db->query("SELECT longitude, latitude from user where user_id = '$user_id' ");
		$row = $result->fetch_assoc();
		return $row;
	}


	function getBooks($user_id, $type){
		$db = connect_db();
		$result = $db->query("SELECT *
								FROM mybooks, books
								WHERE mybooks.isbn = books.isbn
								AND user_id = '$user_id'
								AND type = '$type' ");
		$res['sum'] = $result->num_rows;

		if ($res['sum'] >= 1) {
			while ($res['data'][] = $result->fetch_assoc()) {

			}
			$res['status'] = true;
	           	$res['message'] = "Found";
	        }else{
	            $res['status'] = false;
	            $res['message'] = "Books are empty";
	        }
	        return $res;
	}

	function update_location($user_id, $books_id){
		$db = connect_db();

		$location = $this->get_location($user_id);
		$latitude = $location['latitude'];
		$longitude = $location['longitude'];

		$query = $db->query("UPDATE mybooks 
								SET longitude = '$longitude', latitude = '$latitude' 
								WHERE user_id = '$user_id'
								AND mybooks_id = '$books_id'  ");
		if ($query) {
			$hasil['status'] = true;
			$hasil['message'] = "Location udated";
		}else{
			$hasil['status'] = false;
			$hasil['message'] = "Someting wrong";
		}
		
		return $hasil;
	}

	function postBooks($user_id, $type, $isbn, $description, $status, $sell, $price){
		$db = connect_db();
		$location = $this->get_location($user_id);
		$lat = $location['latitude'];
		$long = $location['longitude'];

		$result = $db->query("INSERT INTO mybooks(user_id, isbn, type, status, sell, price, description, longitude, latitude)
								VALUES ('$user_id', '$isbn', '$type', '$status', '$sell', '$price', '$description', '$long', '$lat')
			");

		if ($result) {
			$res['status'] = true;
			$res['message'] = "Success";
		}else{
			$res['status'] = false;
			$res['message'] = "Failed";
			$res['isbn'] = $isbn;
			$res['error'] = $db->error;
		}

		return $res;
	}

}
