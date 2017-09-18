(function ($) {
  'use strict';
  Drupal.behaviors.questionnaire = {
    attach: function(context, settings) {
      // start function
      var url = window.location.pathname;
      var questionnaireId = '';
      var cookieLife = null;
        // get question
      $.post("/ajaxquestionnairelinks", function(data, status){
          if (status) {
            checkPage(data);
          }
      });

      function checkPage(links) {

        $.each(links, function( i, link ) {
          if (url == link.link) {
            questionnaireId = link.questionnaireId;
            if (isCookie(link.link)) {
              getQuestionnaire(link.questionnaireId);
            }
            console.log('iscookie'+isCookie(link.questionnaireId, link.link));
          }
        });
      }

      function getQuestionnaire(id) {
        $.post("/ajaxquestionnaire", {id: id}, function(data, status){
          if (status) {
            showQuestionnaire(data);
            cookieLife = parseInt(data.days);
          }
        });
      }

      function showQuestionnaire(qnaire) {
        var part1 = '<div id="myModal" class="modal"><div class="modal-content"><form id="questionnaireForm" method="post"><div class="modal-header"><span class="close"><input type="submit" name="" value="X"></span><h4>' + qnaire.description + '</h4></div><div class="modal-body">';
        var part2 = getQuestions(qnaire.questions);
        var part3 = '<input type="submit" value="Skicka"></div></form></div></div>';
        $("body").append(part1 + part2 + part3);
        submitQuestionnaire();
      }

      function getQuestions(qs) {
        var questions = '';
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
        var answers = '';
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
          setCookie();
        });
      }

      function isCookie(link) {
        var is = true;
        var cookie;
        if (cookie = $.cookie('questionnaire'+questionnaireId)) {
          var links = cookie.split(',');
          if ($.inArray(link, links)!='-1') {
            is = false;
          }
          else {
            is = true;
          }
        }
        else {
          $.cookie('questionnaire'+questionnaireId, '', { expires : 10 });
          is = true;
        }
        return is;
      }

      function setCookie() {
        var links = $.cookie('questionnaire'+questionnaireId);
        links = links +','+ url;
        $.removeCookie('questionnaire'+questionnaireId);
        $.cookie('questionnaire'+questionnaireId, links, { expires : cookieLife });
      }
    }
  };
}(jQuery));	//ready functuon