<?php

class Booksmodel{
	function search($query){
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
			}
			$hasil['status'] = (bool) 1;
		}else{
			$hasil['total_results'] = (int) 0;
			$hasil['status'] = (bool) 0;
			$hasil['message'] = "Books not found";
		}
		return $hasil;
	}

	function search_books($user_id, $query){
		$db = connect_db();
		$search_exploded = explode (" ", $query); 
		$construct = "";
		foreach( $search_exploded as $search_each) {
             	$construct .="AND keywords LIKE '%$search_each%' ";
		}
		$result = $db->query("SELECT title,mybooks.isbn, author, fullname, mybooks_id, user.user_id, 111045* 
										DEGREES(ACOS(COS(RADIANS(-6.9758829))
							                 * COS(RADIANS(latitude))
							                 * COS(RADIANS(107.6286861) - RADIANS(longitude))
							                 + SIN(RADIANS(-6.9758829))
							                 * SIN(RADIANS(latitude)))) AS distance
								FROM mybooks, books, user 
								WHERE mybooks.isbn = books.isbn 
									AND mybooks.user_id = user.user_id
									AND mybooks.type = 1
									AND mybooks.status = 1
									AND user.user_id != '$user_id'");
	}

	function get_location($user_id){

	}

	function getBooks($user_id, $type, $page){
		$db = connect_db();
		$result = $db->query("SELECT *
								FROM mybooks, books
								WHERE mybooks.isbn = books.isbn
								AND user_id = '$user_id'
								AND type = '$type'
								LIMIT $page , 30 ");
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


}
