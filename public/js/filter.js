window.onload = () => {
    const FiltersForm = document.querySelector("#filters");
    document.querySelectorAll("#filters input").forEach(input => {
        input.addEventListener("change",() => {
            //console.log("clic");
            //ici on intercepte les clics
            //on recupere les donnees du formulaire
            const Form = new FormData(FiltersForm);

            //on fabrique url
            const Params = new URLSearchParams();

            Form.forEach((value, key) => {
                Params.append(key , value);


            });
            //on recupere url active
            const Url = new URL(window.location.href);

            //on lance la requete ajax
            fetch(Url.pathname + "?" +Params.toString() +"&ajax=1",{
                headers:{
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(response =>
                response.json()
            ).then(data => {
                //on va chercher la zone de contenu
               const content = document.querySelector("#content");
               //on remplace le contenu
               content.innerHTML = data.content;
            }).catch(e => alert(e));

        });
        });
}