require('./bootstrap');
const { default: axios } = require('axios');
//const axios = require('axios');
///import Echo from "laravel-echo"


const form = document.getElementsByTagName('form')[0];
const inputField = document.getElementsByTagName("input")[0];
const listContainer = document.getElementById('list');

form.addEventListener('submit', (e) => {
    e.preventDefault();
    const text = inputField.value;
    axios.post('/re', {
        message: text,
    })
});

//Echo.channel

const channel = echo.channel('public.customers');
channel.subscribed(() => {
    alert("you have subscribed")
}).listen('.customers', (event) => {
    let li = document.createElement('li');
    li.classList.add("list-item");
    li.innerHTML = event.name;
    listContainer.appendChild(li);
})

