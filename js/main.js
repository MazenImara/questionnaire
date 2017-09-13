jQuery(document).ready(function($) {

// get question
	$.post("/ajaxquestionnairelinks", function(data, status){	
		  if (status) {
		  	checkPage(data);
		  }
	});


	function checkPage(links) {
		var url = window.location.pathname;
		$.each(links, function( i, link ) {
			if (url == link.link) {
				getQuestionnaire(link.questionnaireId);
			}
		});
	}



	function getQuestionnaire(id) {
		$.post("/ajaxquestionnaire", {id: id}, function(data, status){	
			if (status) {
				showQuestionnaire(data);
			}	  
		});
	}







	function showQuestionnaire(qnaire) {
		var part1 = '<div id="myModal" class="modal"><div class="modal-content"><form id="questionnaireForm" method="post"><div class="modal-header"><span class="close"><input type="submit" name="" value="X"></span><h4>' + qnaire.description + '</h4></div><div class="modal-body">';
		var part2 = getQuestions(qnaire.questions);
		var part3 = '</div></form></div></div>';
		$("body").append(part1 + part2 + part3);
		submitQuestionnaire();
	}

	function getQuestions(qs) {
		questions = '';
		$.each(qs, function( i, q ) {
		  questions = questions + '<p>' + q.body + '</p>' + getAnswers(q.answers);
		});		
		 
		return questions;
	}

	function getAnswers(as) {
		answers = '';
		$.each(as, function( i, a ) {
		  answers = answers + '<input type="radio" required name="'+ a.questionId +'" value="' + a.body + '"> ' + a.body + '<br>';
		});		
		return answers;
	}













	function submitQuestionnaire() {
		$('.modal').show();
		$("#questionnaireForm").submit(function(e) {
	    e.preventDefault();
	    $('.modal').hide();
  	});
	}
	










//for popup	
  


// end popup
});	//ready functuon