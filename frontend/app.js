$(document).ready(function() {

	// Init a gobal timeout variable to be used on scrolling detection
	var loadingFromScroll = false;

	// Init a gobal timeout variable to be used on search trigger detection
	var loadingFromSearch = false;

	// Init a gobal timeout variable to be used on search input keystroke
	var keyupTimeout = null;


	/*******************************************************************************************
	 * FUNCTIONS
	 *******************************************************************************************/

	/**
	 * Function used to toogle the content between search and upcoming
	 *
	 * @param      {string}  type    The type (search or upcoming)
	 */
	let toogleContent = function(type){
		console.log('toogle: '+type)
		//If aiming to display search results content
		if(type == 'search'){
			//Check if upcoming content is visible
			if($('#upcoming-items-container').is(':visible')){
				//If so, hide upcoming content...
				$('#upcoming-items-container').hide('slow', function(){
					//... and after its animation is completed, show search results
					$('#search-items-container').show('slow');
				});
			}
		//Else (if aiming at upcoming)
		} else {
			//Check if search content is visible
			if($('#search-items-container').is(':visible')){
				//If so, hide search content...
				$('#search-items-container').hide('slow', function(){
					//... and after its animation is completed, show upcoming movies
					$('#upcoming-items-container').show('slow');
				});
			}
		}
	}

	/**
	 * Function used to fill a hardcoded model of the movie item with the
	 * request results
	 *
	 * @param      {object}  itemData  The movie data from the request
	 * @return     {string}  The filled model HTML
	 */
	let fillModel = function(itemData){
		//Check for poster iamge path. If null, use a random image from internet to display that the movie has no poster. If not null, prepend the TMDb url to the path
		let posterPath = (itemData.poster_path == null) ? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTWAFqXrJQ-ZGNDEA0AVJXXF2p55MWpuHRujST0xH9qFjiFNpln' : 'https://image.tmdb.org/t/p/w300/'+itemData.poster_path;
		//Get release date
		let releaseDate = (itemData.release_date != '') ? new Date(itemData.release_date) : '-';
		//Convert the date to the new format
		let convertedDate = (releaseDate != '-') ? (releaseDate.getMonth()+1) + "/" + releaseDate.getDate()  + "/" + releaseDate.getFullYear() : '-';
		//Fill the model and return
		return `
		<div class="card w-100 mx-2 mb-3">
			<div class="row no-gutters">
				<div class="col-sm-4 col-md-2">
					<img src="${posterPath}" class="card-img-top img-fluid" alt="${itemData.title}">
				</div>
				<div class="col">
					<div class="card-body">
						<h5 class="card-title">${itemData.title}</h5>
						<p class="card-text">${itemData.overview}</p>
						<p class="card-text"><small class="text-muted">Release date: ${convertedDate}</small></p>
						<a href="#" data-value="${itemData.id}" class="btn btn-outline-info btn-movie-more">Read more</a>
					</div>
				</div>
			</div>
		</div>
		`;
	}


	/**
	 * Function used to fill the info modal with the movie data
	 *
	 * @param      {object}  itemData  The movie data from the request
	 */
	let fillModal = function(itemData){
		//Check for poster iamge path. If null, use a random image from internet to display that the movie has no poster. If not null, prepend the TMDb url to the path
		let posterPath = (itemData.poster_path == null) ? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTWAFqXrJQ-ZGNDEA0AVJXXF2p55MWpuHRujST0xH9qFjiFNpln' : 'https://image.tmdb.org/t/p/w500/'+itemData.poster_path;
		//Get release date
		let releaseDate = (itemData.release_date != '') ? new Date(itemData.release_date) : '-';
		//Convert the date to the new format
		let convertedDate = (releaseDate != '-') ? (releaseDate.getMonth()+1) + "/" + releaseDate.getDate()  + "/" + releaseDate.getFullYear() : '-';
		//Convert the budget
		let budget = (parseInt(itemData.budget) > 0) ? '$' + parseFloat(itemData.budget, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString() : '-';
		//Conver the website
		let homepage = (itemData.homepage != '') ? `<a href='${itemData.homepage}' target='_blank' title='Open ${itemData.title} website'>${itemData.homepage}</a>` : '-';
		//Convert the genres
		let genres = (itemData.genres.length > 0) ? itemData.genres.map(genre => (genre.name)).join(", ") : '-';
		//Convert the companies
		let companies = (itemData.production_companies.length > 0) ? itemData.production_companies.map(company => (company.name)).join(", ") : '-';
		//Update title
		$('#movieModalTitle').text(itemData.title);
		//Update poster image
		$('#movieModalPosterImg').attr('src',posterPath);
		//Update overview
		$('#movieModalOverview').text(itemData.overview);
		//Update release date
		$('#movieModalReleaseDate').text(convertedDate);
		//Update duration
		$('#movieModalDuration').text(itemData.runtime + ' min');
		//Update IMDB rank
		$('#movieModalIMDBRank').text(itemData.vote_average + ' (' + itemData.vote_count + ' votes)');
		//Update budget
		$('#movieModalBudget').text(budget);
		//Update genres
		$('#movieModalGenres').text(genres);
		//Update companies
		$('#movieModalCompanies').text(companies);
		//Update homepage
		$('#movieModalHomepage').html(homepage);
	}

	/**
	 * Function used to update the results of a content container (search or
	 * upcoming) based on a response data from a request
	 *
	 * @param      {string}  type    The type of content (search or upcoming)
	 * @param      {object}  data    The data from the request
	 */
	let updateResults = function(type, data){
		//If aiming to update search results
		if(type == 'search'){
			//Update title
			$('#content-title').text((data.total_results>0) ? `Results for ${$('#search-term').val()}` : `No results found for ${$('#search-term').val()}`);
			//Iterate through results
			for(var i=0; i<data.results.length; i++) { $('#search-items-container').append(fillModel(data.results[i])); }
			//Update page number
			$('#search-content-page').val(data.page);
			//Update max page
			$('#search-content-max-page').val(data.total_pages);
			//Display content
			toogleContent('search');
		//Else (if aiming to update upcoming movies)
		} else {
			//Update title
			$('#content-title').text('Upcoming movies');
			//Iterate through results
			for(var i=0; i<data.results.length; i++) { $('#upcoming-items-container').append(fillModel(data.results[i])); }
			//Update page number
			$('#upcoming-content-page').val(data.page);
			//Update max page
			$('#upcoming-content-max-page').val(data.total_pages);
			//Display content
			toogleContent('upcoming');
		}
		//Reset scroll loading flag
		loadingFromScroll = false;
	}

	/**
	 * Function used to load upcoming movies (given a page). It also allows to
	 * disable the loading overlay modal
	 *
	 * @param      {(number|string)}  [page=1]        The results page to be
	 *                                                requested
	 * @param      {boolean}          [overlay=true]  Flag to define if the
	 *                                                overlay model should be
	 *                                                shown (true) or not
	 *                                                (false)
	 */
	let loadUpcoming = function(page = 1, overlay = true){
		//If overlay flag is set, display loading modal
		if(overlay)	$('#loading_modal').modal();
		//Perfom an AJAX request
		$.ajax({
			//To the upcoming backend URL, with the given page
			url: '../backend/upcoming/'+page,
			//On success callback
			success: function(response){
				//Parse JSON response
				response = JSON.parse(response);
				//If overlay flag is set, hide loading modal
				if(overlay)	$('#loading_modal').modal('hide');
				//Check if registered successfully
				if(response.success == 1){
					//Update response results
					updateResults('upcoming',response.data);
				} else {
					//Display an error message
					alert(response.msg);
				}
			},
			//On error callback
			error: function(jqXHR, status, error){
				//If overlay flag is set, hide loading modal
				if(overlay)	$('#loading_modal').modal('hide');
				//Display an error message
				alert('There was an error during the ajax request.\n\nDetails:\nStatus: '+status+'\nError: '+error);
			}
		});
	}

	/**
	 * Function used to load the next upcoming movies page. It should be called
	 * everytime user hits the bottom of the upcoming movies.
	 */
	let loadNextUpcomingPage = function(){
		//Get current page
		let currentPage = parseInt($('#upcoming-content-page').val());
		//Get last (max) page
		let maxPage = parseInt($('#upcoming-content-max-page').val());
		//If did not reach last page yet, load next upcoming page
		if(currentPage < maxPage) loadUpcoming(currentPage + 1, false);
	}

	/**
	 * Function used to load search results (given a term). It also allows to
	 * determine the results page to load and to disable the loading overlay
	 * modal
	 *
	 * @param      {string}           [terms='']      The search terms
	 * @param      {(number|string)}  [page=1]        The results page to be
	 *                                                requested
	 * @param      {boolean}          [overlay=true]  Flag to define if the
	 *                                                overlay model should be
	 *                                                shown (true) or not
	 *                                                (false)
	 */
	let loadSearch = function(terms = '', page = 1, overlay = true){
		//If a not empty search term was given
		if(terms!=''){
			//If overlay flag is set, display loading modal
			if(overlay)	$('#loading_modal').modal();
			//Perfom an AJAX request
			$.ajax({
				//To the upcoming backend URL, with the given page
				url: '../backend/search/'+terms+'/'+page,
				//On success callback
				success: function(response){
					//Parse JSON response
					response = JSON.parse(response);
					//If overlay flag is set, hide loading modal
					if(overlay)	$('#loading_modal').modal('hide');
					//Reset flag
					loadingFromSearch = false;
					//Check if registered successfully
					if(response.success == 1){
						//Get previous search terms
						let oldTerms = $('#last-search-term').val();
						//If they're different, clear previous results
						if(terms != oldTerms) $('#search-items-container').html('');
						//Update last term
						$('#last-search-term').val(terms);
						//Display response results
						updateResults('search',response.data);
					} else {
						//Display error message
						alert(response.msg);
					}   
				},
				//On error callback
				error: function(jqXHR, status, error){
					//Hide loading modal overlay
					$('#loading_modal').modal('hide');
					//Display error message
					alert('There was an error during the ajax request.\n\nDetails:\nStatus: '+status+'\nError: '+error);
				}
			});
		}
	}

	/**
	 * Prepare a search
	 */
	let prepareSearch = function(){
		// Clear the keyup timeout if it has already been set
		clearTimeout(keyupTimeout);
		//Get current search terms
		let terms = $('#search-term').val();
		//If the search term is not empty
		if(terms != ''){
			//Check if not already loading from search
			if(!loadingFromSearch){
				//Set flag
				loadingFromSearch = true;
				//Perform a new search
				loadSearch(terms);
			}
		//If the search term is empty
		} else {
			//Display upcoming movies
			toogleContent('upcoming');
		}
	}

	/**
	 * Function used to load the next search results page. It should be called
	 * everytime user hits the bottom of the search results.
	 */
	let loadNextSearchPage = function(){
		//Get current page
		let currentPage = parseInt($('#search-content-page').val());
		//Get last (max) page
		let maxPage = parseInt($('#search-content-max-page').val());
		//Get search terms
		let terms = $('#search-term').val();
		//If did not reach last page yet, load next search page
		if(currentPage < maxPage) loadSearch(terms, currentPage + 1, false);
	}

	/**
	 * Function used to load a single movie info (given an ID). It also allows
	 * to disable the loading overlay modal
	 *
	 * @param      {id}       [id=0]          The movie ID
	 * @param      {boolean}  [overlay=true]  Flag to define if the overlay
	 *                                        model should be shown (true) or
	 *                                        not (false)
	 */
	let loadMovie = function(id = 0, overlay = true){
		//If overlay flag is set, display loading modal
		if(overlay)	$('#loading_modal').modal();
		// Perfom an AJAX request
		$.ajax({
			//To the upcoming backend URL, with the given page
			url: '../backend/movie/'+id,
			//On success callback
			success: function(response){
				//Parse JSON response
				response = JSON.parse(response);
				//If overlay flag is set, hide loading modal
				if(overlay)	$('#loading_modal').modal('hide');
				//Check if registered successfully
				if(response.success == 1){
					//Fill the modal
					fillModal(response.data);
					//And display it
					$('#movie_modal').modal();
				} else {
					//Display error message
					alert(response.msg);
				}   
			},
			//On error callback
			error: function(jqXHR, status, error){
				//Hide loading modal overlay
				$('#loading_modal').modal('hide');
				//Display error message
				alert('There was an error during the ajax request.\n\nDetails:\nStatus: '+status+'\nError: '+error);
			}
		});
	}

	/*******************************************************************************************
	 * EVENT LISTENERS
	 *******************************************************************************************/

	/**
	 * Event detection on 'Read more' button from movie info
	 */
	$('body').on('click', '.btn-movie-more', function(){
		//Get moview ID from item data attribute
		let movieID = $(this).data("value");
		//Load movie info
		loadMovie(movieID);
		//Return false to the anchor tag
		return false;
	});

	/**
	 * Event detection on keystroke on search box
	 */
	$('#search-term').on('keyup', function(){
		// Clear the timeout if it has already been set
		clearTimeout(keyupTimeout);
		// Make a new timeout
		keyupTimeout = setTimeout(function () {
			//Prepare for searching
			prepareSearch();
		//Interval of 1000ms
		}, 1000);
	});

	/**
	 * Event detection on keypress on search box
	 */
	$('#search-term').on('keypress',function(e) {
		//If its an ENTER (return) key
		if(e.which == 13) {
			//Prepare for searching
			prepareSearch();
		}
	});

	/**
	 * Event detection on search button
	 */
	$('#submitSearch').on('click', function(){
		//Prepare for searching
		prepareSearch();
	});

	/**
	 * Event detection on page scrolling
	 */
	$(window).scroll(function() {
		//If user has reach last 75% of page height
		if($(window).scrollTop() + $(window).height() >= $(document).height()*0.75) {
			//If not loading from scroll yet
			if(!loadingFromScroll){
				//Set flag
				loadingFromScroll = true;
				//If upcoming movies are visible
				if($('#upcoming-items-container').is(':visible')){
					//Load next upcoming page
					loadNextUpcomingPage();	
				//Else (if search results are visible)
				} else {
					//Load next search page
					loadNextSearchPage();
				}
			}
		}
	});

	/*******************************************************************************************
	 * INIT
	 *******************************************************************************************/

	//Load upcoming events on page first loading
	loadUpcoming();
});