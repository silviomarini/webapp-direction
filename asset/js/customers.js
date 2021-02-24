setInterval(getPolls(), 3000);


function submitQuestion(){
    question = document.getElementById('question').value;
    if(question != ''){
    fetch('api/post-question.php', {
        method: 'POST',
        headers: {
            'content-type': 'application/json'
        },
        body: JSON.stringify( {"name": question })
        })
        .then(response => {
            console.log(response);
            document.getElementById('question').value = "";
            document.getElementById('alertQuestion').style.display="block";
            setTimeout(function() {
                $("#alertQuestion").hide();
            }, 3000);
        })
        .catch(err => {
            console.log(err)
        })
    } else {
        alert("please fill the question box!");
    }
}

function switchTab(tab){
    if(tab == 'polls'){
        document.getElementById('polls').style.display="block";
        document.getElementById('pollsMenu').classList.add("active");
        document.getElementById('questions').style.display="none";
        document.getElementById('questionsMenu').classList.remove("active");
        getPolls();
    } else {
        document.getElementById('polls').style.display="none";
        document.getElementById('pollsMenu').classList.remove("active");
        document.getElementById('questions').style.display="block";
        document.getElementById('questionsMenu').classList.add("active");
    }
}

function getPolls(){
    setInterval(function(){ 
        fetch('api/get-polls.php')
        .then(function (response) {
            // The API call was successful!
            return response.text();
        }).then(function (html) {
            // This is the HTML from our response as a text string
            console.log(html);
            document.getElementById('currentPoll').innerHTML = html;
        }).catch(function (err) {
            // There was an error
            console.warn('Something went wrong.', err);
            document.getElementById('currentPoll').innerHTML = "<div class='text-center fontWeight700 pb-1 pt-1'> Wait the next poll... </div>";
        });
    }, 5000);
}

 
    // AJAX SALVATAGGIO DOMANDA A RISPOSTA APERTA
    function postOpenPoll(){
        var risposta_aperta = $('#risposta_aperta').val();
        risposta_aperta = encodeURIComponent(risposta_aperta);

        var current_event_id = $('#current_event_id').val();
        var ultimo_ID = $("#ultimo_ID").val();
        var id_sondaggio = $('#id_sondaggio').val();

        if(risposta_aperta!=""){
            $.ajax({
                url: "api/post-answer.php",
                type: "get",
                crossDomain: true,
                data: 'azione=risp_aperta&risposta_aperta=' + risposta_aperta + '&ultimo_ID='+ultimo_ID+ '&current_event_id='+current_event_id+ '&id_sondaggio='+id_sondaggio,
                success: function(data){
                    //console.log(data);
                    $("#alertDomanda").show();
                    $("#contDomande").hide();
                    $("#risposta_aperta").val('');
                    getPolls();
                },
                error: function () {
                    alert('Impossible to send the answer, please retry later.');
                }
            });	
        }else{
            alert("Please enter an answer");	
        }

    }

    // AJAX SALVATAGGIO DOMANDA A RISPOSTA MULTIPLA
    function postClosedPoll(){
        var radioValue = $("input[name='risposte_s']:checked").val();
        var id_sondaggio= $("#id_sondaggio").val();
            var ultimo_ID = $("#ultimo_ID").val();
        var current_event_id = $('#current_event_id').val();

            $.ajax({
                url: "api/post-answer.php",
                type: "get",
                crossDomain: true,
                data: 'azione=risp_multipla&risposta=' + radioValue + '&ultimo_ID='+ultimo_ID+ '&current_event_id='+current_event_id+ '&id_sondaggio='+id_sondaggio,
                success: function(data){
                    console.log(data);
                    $("#alertDomanda").show();
                    $("#contDomande").hide();
                    //$("#risposta_aperta").val('');
                    getPolls();
                },
                error: function () {
                    //alert('Errore AJAX');
                }
            });		
    }

    $(".close").on('click', function(event){
        $( ".submit-response" ).hide();
    });