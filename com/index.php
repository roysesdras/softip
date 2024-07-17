
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soft IP Technologie forum</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- For code source folder -->
  <link href="style.css" rel="stylesheet" type="text/css">
  <link href="comments.css" rel="stylesheet" type="text/css">

</head>
<body>
  <div class="container-fluide">
    <nav class="navtop">
      <div>
        <h1>Soft IP Technologie forum</h1>
      </div>
    </nav>
  </div>
  <!-- ============= contenus container =============== -->
  <div class="container">
      <div class="content home">
          <h2>Article</h2>
          <p>
            Morbi purus turpis, finibus vel fermentum nec, egestas eu elit. Fusce eleifend ac massa sit amet eleifend. Suspendisse sit amet facilisis augue. Praesent vitae dui lacus. Suspendisse sodales nisl massa, quis vehicula ante rutrum vitae. Proin scelerisque vestibulum purus, ac ultrices sapien malesuada vel. Proin sit amet tristique orci. Vestibulum in tortor ante.
          </p>
      </div>

      <div class="comments"></div>
  </div>
  

      <script>
            const comments_page_id = 1; // This number should be unique on every page

            fetch("comments.php?page_id=" + comments_page_id).then(response => response.text()).then(data => {

                    document.querySelector(".comments").innerHTML = data;

                    document.querySelectorAll(".comments .write_comment_btn, .comments .reply_comment_btn").forEach(element => {

                          element.onclick = event => {

                                  event.preventDefault();

                                  document.querySelectorAll(".comments .write_comment").forEach(element => element.style.display = 'none');

                                  document.querySelector("div[data-comment-id='" + element.getAttribute("data-comment-id") + "']").style.display = 'block';

                                  document.querySelector("div[data-comment-id='" + element.getAttribute("data-comment-id") + "'] input[name='name']").focus();

                            };

                    });

                    document.querySelectorAll(".comments .write_comment form").forEach(element => {

                          element.onsubmit = event => {

                                  event.preventDefault();

                                  fetch("comments.php?page_id=" + comments_page_id, {

                                          method: 'POST',

                                          body: new FormData(element)

                                  }).then(response => response.text()).then(data => {

                                          element.parentElement.innerHTML = data;

                                  });

                            };

                    });

            });

      </script>
 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>

 

                