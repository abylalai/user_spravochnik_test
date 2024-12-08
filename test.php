<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Directory</title>
    <link rel="stylesheet" href="https://cdn.webix.com/edge/webix.css" type="text/css">
    <script src="https://cdn.webix.com/edge/webix.js" type="text/javascript"></script>
</head>
<body></body>
</html>

<script>
    webix.ready(function () {
    const apiUrl = "/api.php";

    // User Form
    const userForm = {
        view: "form",
        id: "userForm",
        elements: [
            { view: "text", name: "full_name", label: "Full Name", required: true },
            { view: "text", name: "login", label: "Login", required: true },
            { view: "text", name: "password", label: "Password", required: true },
            { view: "select", name: "role_id", label: "Role", options: [], required: true },
            {
                cols: [
                    { view: "button", value: "Save", click: saveUser },
                    { view: "button", value: "Cancel", click: () => $$("userFormWindow").hide() },
                ],
            },
        ],
    };


    const userFormWindow = {
        view: "window",
        id: "userFormWindow",
        width: 400,
        position: "center",
        modal: true,
        head: "User Form",
        body: userForm,
    };

    // Main Layout
    webix.ui({
        rows: [
            {
                view: "toolbar",
                elements: [
                    { view: "button", value: "Add User", click: () => showUserForm() },
                ],
            },
            {
                view: "datatable",
                id: "userTable",
                columns: [
                    { id: "full_name", header: "Full Name", fillspace: true },
                    { id: "login", header: "Login", fillspace: true },
                    { id: "role_name", header: "Role", fillspace: true },
                    { id: "is_blocked", header: "Blocked", template: obj => obj.is_blocked ? "Yes" : "No", width: 100 },
                    {
                        header: "",
                        template: "<button class='block-btn'>Block</button>",
                        width: 100,
                    },
                ],
                onClick: {
                    "block-btn": function (e, id) {
                        blockUser(id.row);
                        return false;
                    },
                },
            },
        ],
    });

    webix.ui(userFormWindow);

    // Fetch Data
    function fetchData() {
        webix.ajax(`${apiUrl}?action=getUsers`, function (text, data) {
            $$("userTable").clearAll();
            $$("userTable").parse(data.json());
        });

        webix.ajax(`${apiUrl}?action=getRoles`, function (text, data) {
            $$("userForm").elements.role_id.define("options", data.json());
            $$("userForm").elements.role_id.refresh();
        });
    }

    // Show Form
    function showUserForm(user = null) {
        $$("userForm").clear();
        if (user) {
            $$("userForm").setValues(user);
        }
        $$("userFormWindow").show();
    }

    // Save User
    function saveUser() {
    const values = $$("userForm").getValues();
    console.log("Form values before sending:", values);

    webix.ajax().headers({ "Content-Type": "application/json" }).post(
        `${apiUrl}?action=createUser`,
        JSON.stringify(values), // Данные преобразуются в JSON
        function (response) {
            console.log("Response from server:", response);
            fetchData();
            $$("userFormWindow").hide();
        }
    );
}



    // Block User
    function blockUser(userId) {
        webix.ajax().post(`${apiUrl}?action=blockUser&id=${userId}`, function () {
            fetchData();
        });
    }

    fetchData();
});

</script>