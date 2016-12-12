<?php
	include( "getMovie.php" );

	function getRecommendation( &$movieIDList )
	{
		$selectedRecommendation = array();

		$genreArray = array('documentary', 'action', 'comedy', 'drama', 'adventure', 'horror', 'crime', 'mystery', 'scifi', 'animation', 'fantasy', 'thriller', 'biography', 'western', 'family', 'musical', 'romance', 'music', 'filmNoir', 'sport', 'history', 'war', 'short', 'news'  );

		$exceptMovieIDList = $movieIDList;
		$movieRows = getAllMovieExceptID( $exceptMovieIDList );

		for( $i=0 ; $i<count($movieIDList) ; $i++ )
		{
			$row = getMovieByMovieID( $movieIDList[$i] );
			$sortedRecommendation = array();

			for( $j=0 ; $j<count($movieRows) ; $j++ )
			{
				if( $row[ 'movieName' ] != $movieRows[$j][ 'movieName' ] )
				{
					$distance = 0;

					// Director Name Distance
					if( $row[ 'directorName' ] != $movieRows[$j][ 'directorName' ] )
					{
						$distance += 1;
					}

					// Genre Distance
					for( $k=0 ; $k<count($genreArray) ; $k++ ) 
					{
						$distance += pow( $row[ $genreArray[$k] ] - $movieRows[$j][ $genreArray[$k] ], 2 );
					}

					// Location Distance
					if( $row[ 'location' ] != $movieRows[$j][ 'location' ] )
					{
						$distance += 1;
					}

					// Year Distance
					if( $row[ 'year' ] < 2000 )
					{
						$movie1YearValue = 0;
					}
					elseif( $row[ 'year' ] > 2010 )
					{
						$movie1YearValue = 2;
					}
					else
					{
						$movie1YearValue = 1;
					}

					if( $movieRows[$j][ 'year' ] < 2000 )
					{
						$movie2YearValue = 0;
					}
					elseif( $movieRows[$j][ 'year' ] > 2010 )
					{
						$movie2YearValue = 2;
					}
					else
					{
						$movie2YearValue = 1;
					}

					$distance += pow( $movie1YearValue - $movie2YearValue, 2 );

					$sortedRecommendation += array( $movieRows[$j][ 'movieID' ] => $distance );
				}
			}

			asort($sortedRecommendation);
			$n=1;
			foreach( $sortedRecommendation as $key => $value )
			{
				array_push($selectedRecommendation, $key);
				array_push($exceptMovieIDList, $key);
				$movieRows = getAllMovieExceptID( $exceptMovieIDList );
				if( $n == 2 )
				{
					break;
				}
				$n++;
				next($sortedRecommendation);
			}
		}
		return $selectedRecommendation;
	}
?>