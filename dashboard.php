<?php
session_start();
include 'db2.php';

if ($_SESSION['role'] != 'admin') die("Access restricted to admin only");

// Handle AJAX requests for update, delete, add, fetch user details
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'update') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password_hash = $_POST['password_hash'];
        $role = $_POST['role'];
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password_hash=?, role=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $email, $password_hash, $role, $status, $id);
        if ($stmt->execute()) echo "updated"; else echo "failed";
        exit;
    }
    if ($_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) echo "deleted"; else echo "failed";
        exit;
    }
    if ($_POST['action'] == 'add') {
        // hash password before insert
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $status = $_POST['status'];
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password_hash, $role, $status);
        if ($stmt->execute()) echo "added"; else echo "failed";
        exit;
    }
    if ($_POST['action'] == 'get') {
        $id = $_POST['id'];
        $res = $conn->query("SELECT * FROM users WHERE id=$id");
        echo json_encode($res->fetch_assoc());
        exit;
    }
}

// Fetch all users
$res = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<style>
body { font-family: 'Poppins', sans-serif; background: #f4f6f8; }
header { background: linear-gradient(135deg, #0984e3, #6c5ce7); color: white; text-align: center; padding: 25px 0; font-size: 24px; }
table { border-collapse: collapse; width: 90%; margin: 30px auto; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden; }
th, td { padding: 14px 15px; text-align: center; border-bottom: 1px solid #eee; }
th { background: #0984e3; color: white; }
tr:hover { background-color: #dfe6e9; }
input[type="text"], input[type="email"], input[type="password"], select { border: 1px solid #ccc; border-radius: 6px; padding: 5px 8px; width: 90%; font-size: 15px; }
button { border: none; padding: 8px 12px; border-radius: 6px; color: white; cursor: pointer; font-size: 14px; margin: 2px; }
.save-btn { background: #00b894; }
.edit-btn, .show-btn { background: #0984e3; }
.add-btn { background: #6c5ce7; margin-bottom: 10px; }
.delete-btn { background: #d63031; }
button:hover { opacity: 0.9; }
footer { text-align: center; margin-top: 20px; color: #636e72; }
.status-msg { text-align: center; font-weight: bold; margin-top: 10px; }
.modal { display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); justify-content:center; align-items:center; z-index:1000; }
.modal-content { background:white; padding:22px 18px; border-radius:8px; min-width:340px; max-width:95vw;}
.modal-header { font-weight:bold; font-size:18px; margin-bottom:14px;}
.modal label { display:block; margin:8px 0 2px 0;}
.modal input, .modal select { width: 100%; }
.modal .close { background: #d63031; float:right; }
</style>
</head>
<body>
<header>Admin Dashboard</header>
<div class="status-msg" id="msg"></div>

<div style="width:90%;margin:auto;text-align:right;">
    <button class="add-btn" onclick="showAddModal()">+ Add User</button>
</div>

<table id="userTable">
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Role</th>
    <th>Status</th>
    <th>Action</th>
</tr>
<?php while ($r = $res->fetch_assoc()) { ?>
<tr id="row<?= $r['id'] ?>">
    <td><?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= htmlspecialchars($r['role']) ?></td>
    <td><?= htmlspecialchars($r['status']) ?></td>
    <td>
        <button class="show-btn" onclick="showDetails(<?= $r['id'] ?>)">Show Details</button>
        <button class="edit-btn" onclick="showEditModal(<?= $r['id'] ?>)">Edit</button>
        <button class="delete-btn" onclick="deleteUser(<?= $r['id'] ?>)">Delete</button>
    </td>
</tr>
<?php } ?>
</table>

<footer>&copy; <?= date('Y') ?> Admin Panel. All rights reserved.</footer>

<!-- Add User Modal -->
<div class="modal" id="addModal">
    <div class="modal-content">
        <div class="modal-header">Add New User <button onclick="closeAddModal()" class="close">X</button></div>
        <label>Name</label>
        <input type="text" id="addName">
        <label>Email</label>
        <input type="email" id="addEmail">
        <label>Password</label>
        <input type="password" id="addPassword">
        <label>Role</label>
        <select id="addRole">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <label>Status</label>
        <select id="addStatus">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <button class="save-btn" onclick="addUser()">Add User</button>
    </div>
</div>

<!-- Show Details Modal -->
<div class="modal" id="detailsModal">
    <div class="modal-content" id="detailsModalContent">
        <!-- content filled in JS -->
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">Edit User <button onclick="closeEditModal()" class="close">X</button></div>
        <input type="hidden" id="editId">
        <label>Name</label>
        <input type="text" id="editName">
        <label>Email</label>
        <input type="email" id="editEmail">
        <label>Password Hash</label>
        <input type="text" id="editPasswordHash">
        <label>Role</label>
        <select id="editRole">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <label>Status</label>
        <select id="editStatus">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <button class="save-btn" onclick="saveEditUser()">Save Changes</button>
    </div>
</div>

<script>
function showAddModal() { document.getElementById('addModal').style.display='flex'; }
function closeAddModal() { document.getElementById('addModal').style.display='none'; }
function addUser(){
    const name = document.getElementById('addName').value;
    const email = document.getElementById('addEmail').value;
    const password = document.getElementById('addPassword').value;
    const role = document.getElementById('addRole').value;
    const status = document.getElementById('addStatus').value;
    fetch("",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`action=add&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&role=${encodeURIComponent(role)}&status=${encodeURIComponent(status)}`
    }).then(res=>res.text()).then(data=>{
        document.getElementById("msg").textContent = data==="added"? "✅ User added!" : "❌ Add failed.";
        setTimeout(()=>{ document.getElementById("msg").textContent=""; },2500);
        closeAddModal();
        if(data==="added") location.reload();
    });
}
function showDetails(id){
    fetch("",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`action=get&id=${id}`
    }).then(res=>res.json()).then(data=>{
        let html = `<div class="modal-header">User Details <button onclick="closeDetailsModal()" class="close">X</button></div>`;
        html += `<div><b>Id:</b> ${data.id}</div>`;
        html += `<div><b>Name:</b> ${data.name}</div>`;
        html += `<div><b>Email:</b> ${data.email}</div>`;
        html += `<div><b>Password Hash:</b> ${data.password_hash}</div>`;
        html += `<div><b>Created At:</b> ${data.created_at}</div>`;
        html += `<div><b>Role:</b> ${data.role}</div>`;
        html += `<div><b>Status:</b> ${data.status}</div>`;
        html += `<div><b>Last Login:</b> ${data.last_login}</div>`;
        document.getElementById('detailsModalContent').innerHTML = html;
        document.getElementById('detailsModal').style.display='flex';
    });
}
function closeDetailsModal(){ document.getElementById('detailsModal').style.display='none'; }

// Show full edit modal
function showEditModal(id){
    fetch("",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`action=get&id=${id}`
    }).then(res=>res.json()).then(data=>{
        document.getElementById('editId').value = data.id;
        document.getElementById('editName').value = data.name;
        document.getElementById('editEmail').value = data.email;
        document.getElementById('editPasswordHash').value = data.password_hash;
        document.getElementById('editRole').value = data.role;
        document.getElementById('editStatus').value = data.status;
        document.getElementById('editModal').style.display='flex';
    });
}
function closeEditModal(){ document.getElementById('editModal').style.display='none'; }
function saveEditUser(){
    const id = document.getElementById('editId').value;
    const name = document.getElementById('editName').value;
    const email = document.getElementById('editEmail').value;
    const password_hash = document.getElementById('editPasswordHash').value;
    const role = document.getElementById('editRole').value;
    const status = document.getElementById('editStatus').value;
    fetch("",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`action=update&id=${id}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password_hash=${encodeURIComponent(password_hash)}&role=${encodeURIComponent(role)}&status=${encodeURIComponent(status)}`
    }).then(res=>res.text()).then(data=>{
        document.getElementById("msg").textContent = data==="updated"? "✅ User updated!" : "❌ Update failed.";
        setTimeout(()=>{ document.getElementById("msg").textContent=""; },2500);
        closeEditModal();
        if(data==="updated") location.reload();
    });
}
function deleteUser(id){
    if(!confirm("Delete this user?")) return;
    fetch("",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`action=delete&id=${id}`
    }).then(res=>res.text()).then(data=>{
        if(data==="deleted"){
            document.getElementById("row"+id).remove();
            document.getElementById("msg").textContent = "🗑️ User deleted!";
            setTimeout(()=>{ document.getElementById("msg").textContent = ""; }, 2500);
        }
    });
}
</script>
</body>
</html>
