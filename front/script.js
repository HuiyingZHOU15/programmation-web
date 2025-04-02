// Description: This file contains the JavaScript code for the front-end of the application.

const webServerAddress = "http://localhost";

document.getElementById("registerForm").addEventListener("submit", function (e) {
	e.preventDefault();
  
	const data = {
	  username: document.getElementById("username").value,
	  email: document.getElementById("email").value,
	  password: document.getElementById("password").value
	};
  
	fetch("http://localhost:8080/register", {
	  method: "POST",
	  headers: {
		"Content-Type": "application/json"
	  },
	  body: JSON.stringify(data)
	})
	  .then(res => res.json())
	  .then(result => {
		if (result.user_id) {
		  document.getElementById("registerResult").innerText = "Inscription réussie 🎉";
		} else {
		  document.getElementById("registerResult").innerText = "Erreur : " + result.error;
		}
	  })
	  .catch(err => {
		document.getElementById("registerResult").innerText = "Erreur réseau";
	  });
  });
  

const form = document.getElementById("post-comment");
// Trigger the getComments function when the form is submitted
form.addEventListener("submit", async (event) => {
	// Prevent the default form submission (page reload)
	event.preventDefault();
	const comments = await sendComment(event);
	event.target.reset();
	await displayComments(comments);
});

const button = document.getElementById("get-comments");
// Trigger the getComments function when the button is clicked
button.addEventListener("click", async () => {
	const comments = await getComments();
	await displayComments(comments);
});

/**
 * This function sends a POST request to the server with the form data to add a new comment.
 * @param {SubmitEvent} event The event that triggered the function
 * @returns {Object} The result of the form submission
 */
async function sendComment(event) {
	const body = new URLSearchParams(new FormData(event.target));

	try {
		// Send a POST request to the server with the form data
		const response = await fetch(`${webServerAddress}/comment`, {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
			},
			// Serialize the form data to URL-encoded format
			body,
		});

		if (response.ok) {
			// If the request was successful, log the result
			const result = await response.json();
			console.log("Form submitted successfully:", result);
			return result;
		} else {
			console.error(
				"Form submission failed:",
				response.status,
				response.statusText
			);
		}
	} catch (error) {
		console.error("Error occurred:", error);
	}
}

/**
 * This function sends a GET request to the server to retrieve all comments.
 */
async function getComments() {
	try {
		// Send a GET request to the server to retrieve all comments
		const response = await fetch(`${webServerAddress}/comment`, {
			method: "GET",
		});

		if (response.ok) {
			const result = await response.json();
			console.log("Comments retrieved successfully:", result);
			return result;
		} else {
			console.error(
				"Comments retrieval failed:",
				response.status,
				response.statusText
			);
		}
	} catch (error) {
		console.error("Error occurred:", error);
	}
}

/**
 * This function takes the list of comments and displays them in the HTML list inside the div with id="comment-list".
 * @param {Array} comments List of comments to display
 */
async function displayComments(comments) {
	
	// 1. Create a new unordered list element (ul)
	const ul = document.createElement("ul");

	// 2. Loop through each comment in the list
	for (let i = 0; i < comments.length; i++) {
		const comment = comments[i];

		// 3. Create a new list item element (li) for each comment
		const li = document.createElement("li");

		// 4. Set the innerHTML of the list item to the comment text
		li.innerHTML = "<strong>" + comment.firstname + " " + comment.lastname + "</strong> : " + comment.message;

		// 5. Append the list item to the unordered list
		ul.appendChild(li);
	}

	// 6. Append the unordered list to the div with id="comment-list"
	const commentListDiv = document.getElementById("comment-list");

	// 清空之前的内容（避免重复添加）
	commentListDiv.innerHTML = "";

	commentListDiv.appendChild(ul);
}
