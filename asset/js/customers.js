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
        document.getElementById('questions').style.display="none";
        getPolls();
    } else {
        document.getElementById('polls').style.display="none";
        document.getElementById('questions').style.display="block";
    }
}

function getPolls(){
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
    });
}