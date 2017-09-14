jQuery(document).ready(function($) {
	var url = window.location.pathname;
// get question
	$.post("/ajaxquestionnairelinks", function(data, status){	
		  if (status) {
		  	checkPage(data);
		  }
	});


	function checkPage(links) {
		
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
			if (q.textAnswer == 1) {
		  	questions = questions + '<p>' + q.body + '<textarea form ="questionnaireForm" name="textArea'+','+url+','+ q.body +'" style="width:100%;"></textarea>';
			}
			else{
		  	questions = questions + '<p>' + q.body + '</p>' + getAnswers(q);
			}
		});		
		 
		return questions;
	}

	function getAnswers(q) {
		answers = '';
		$.each(q.answers, function( i, a ) {
		  answers = answers + '<input type="radio" required name="'+ q.id +'" value="' + url +','+ q.body +','+ a.body + '"> ' + a.body + '<br>';
		});		
		return answers;
	}













	function submitQuestionnaire() {
		$('.modal').show();
		$("#questionnaireForm").submit(function(e) {
	    e.preventDefault();
	    $('.modal').hide();

	    
			$.post("/ajaxaddresult", {answers: $( this ).serializeArray()});

			console.log( {answers: $( this ).serializeArray()} );
			console.log( $( this ).serializeArray());	


  	});
	}
	


if ($.cookie("sss")) {
	alert($.cookie("questionnaire"))
}

//$.cookie("questionnaire", '/pear,/kontakt', { expires : 10 });


//https://stackoverflow.com/questions/1458724/how-do-i-set-unset-cookie-with-jquery


//for popup	
  


// end popup
});	//ready functuon