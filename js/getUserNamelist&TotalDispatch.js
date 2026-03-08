// getUserNamelist&TotalDispatch.js

document.addEventListener('DOMContentLoaded', function () {
    fetch('https://kingslist.pro/app/default/api/getUserNamelist&TotalDispatch.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            const countUserNamelist = data.countUserNamelist;
            const countTotalDispatch = data.countTotalDispatch;

            // Display the totals in the respective HTML elements
            document.getElementById('userNamelistTotal').innerText = countUserNamelist;
            document.getElementById('totalDispatchTotal').innerText = countTotalDispatch;

            // Additional logic for displaying messages and links (if needed)
            let message = "";
            let actionText = "";
            let actionLink = "";

            if (countUserNamelist == 0) {
                message = "To begin, Create a list of Contacts using the link below";
                actionText = "Create List";
                actionLink = "https://kingslist.pro/v2/lists.php#addList";
            } else if (countTotalDispatch == 0) {
                message = "Dispatch your first personalised message";
                actionText = "Dispatch";
                actionLink = "https://kingslist.pro/v2/lists.php";
            } else {
                message = "Dispatch a personalised message";
                actionText = "Dispatch";
                actionLink = "https://kingslist.pro/v2/lists.php";
            }

            // If you have these elements in your HTML, update them accordingly
            document.getElementById('message').innerText = message;
            document.getElementById('action-text').innerText = actionText;
            document.getElementById('action-link').href = actionLink;
        })
        .catch(error => console.error('Error:', error));
});
