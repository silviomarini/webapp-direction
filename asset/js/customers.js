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
    } else {
        document.getElementById('polls').style.display="none";
        document.getElementById('questions').style.display="block";
    }
}