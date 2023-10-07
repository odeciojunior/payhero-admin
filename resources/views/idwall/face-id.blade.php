<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>User validation</title>
    <script src="https://sdkweb-lib.idwall.co/index.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <meta name="csrf-token"
          content="{{ csrf_token() }}">
</head>

<body>
    <div style="display: flex; justify-content: center; align-items: center">
        <div data-idw-sdk-web></div>
    </div>
    <script>
        const user_id = "" + `{{ $user_id }}`;

        idwSDKWeb({
            token: 'U2FsdGVkX19q4ivHZJe2oIXSOmu4Q9fRYwUk5O0ZphLI7Qye+w==',
            onRender: () => {},
            onComplete: ({
                token
            }) => {

                $.ajax({
                    method: "POST",
                    url: "/validate-user",
                    dataType: "json",
                    headers: {
                        Authorization: $('meta[name="csrf-token"]').attr("content"),
                        Accept: "application/json",
                    },
                    data: {
                        user_id,
                        token
                    },
                    cache: false,
                    error: function(response) {
                        console.log("error");
                        console.log(response);
                    },
                    success: function success(data) {

                        window.location.href = data.url_redirect;
                    },
                });
            },
            onError: (error) => {
                alert(error);
            }
        });
    </script>

</body>

</html>
