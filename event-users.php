<?php 
include 'inc/header.php';
include 'inc/sidebar.php';
include 'class/eventuser.php';

try {
    $eventId=$_GET['event_id'];
    if(!isset($_GET['event_id'])|| $_GET['event_id']==null){
        header("Location:index.php");
    }
} catch (Exception $e) {
    echo "Error: " . $e;
    exit();
}

try {
    $eventObj = new eventuser();
    $fm=new format();
} catch (Exception $e) {
    echo "Error: " . $e;
    exit();
}
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'event_date';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';

$eventUsers = $eventObj->getPaginatedUsers($eventId,$page, $limit, $search, $sortField, $sortOrder);
?>
<?php
    if(isset($_GET['deleteUser'])){
        $deleteUserId=preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['deleteUser']);
        $eventDelete=$eventObj->delete($eventId,$deleteUserId);
        header("Location:event-users.php?event_id=$eventId&search=$search&page=$page&limit=$limit&sort=$sortField&order=sortOrder");
    }
?>

    <div class="container mt-4">
        <h2>My Event Users</h2>
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search users...">
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        <button class="btn btn-success" onclick="exportToExcel()">
                            <i class="bi bi-file-earmark-excel"></i> Export to CSV
                        </button>
                    </div>
                </div>
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-striped table-hover " >
                        <thead>
                            <tr>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="name">Name <i class="bi bi-arrow-down-up"></i></a></th>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="email">Email <i class="bi bi-arrow-down-up"></i></a></th>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="mobile">Mobile <i class="bi bi-arrow-down-up"></i></a></th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="eventsTableBody">
                            <?php if (!empty($eventUsers['data'])): ?>
                                <?php foreach ($eventUsers['data'] as $user): ?>
                                    <tr>
                                        <td ><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td ><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td ><?php echo htmlspecialchars($user['mobile']); ?></td>
                                        <td >
                                        <a href="?deleteUser=<?php echo $user['id']; ?>&event_id=<?php echo $user['event_id']; ?>" class="btn btn-danger btn-sm ms-1" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div>
                            <select id="pageSize" class="form-select w-auto" onchange="changePageSize(this.value)">
                                <option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>5 per page</option>
                                <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10 per page</option>
                                <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25 per page</option>
                                <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 per page</option>
                            </select>
                            <small class="text-muted mt-2 d-block">
                                Showing <?php echo ($page - 1) * $limit + 1; ?>-<?php echo min($page * $limit, $eventUsers['total']); ?> 
                                of <?php echo $eventUsers['total']; ?> users
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="Page navigation" class="float-md-end">
                            <ul class="pagination" id="pagination">
                                <?php if ($eventUsers['totalPages'] > 1): ?>
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($page - 1); ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortField; ?>&order=<?php echo $sortOrder; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $eventUsers['totalPages']; $i++): ?>
                                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortField; ?>&order=<?php echo $sortOrder; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $eventUsers['totalPages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($page + 1); ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortField; ?>&order=<?php echo $sortOrder; ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
let currentSort = '<?php echo $sortField; ?>';
let currentOrder = '<?php echo $sortOrder; ?>';
let currentSearch = '<?php echo $search; ?>';

function updateURL(params = {}) {
    const searchParams = new URLSearchParams(window.location.search);
    for (const [key, value] of Object.entries(params)) {
        if (value !== null && value !== '') {
            searchParams.set(key, value);
        } else {
            searchParams.delete(key);
        }
    }
    window.location.href = `?${searchParams.toString()}`;
}

function changePageSize(limit) {
    updateURL({ page: 1, limit });
}

function handleSort(field) {
    const newOrder = field === currentSort && currentOrder === 'ASC' ? 'DESC' : 'ASC';
    updateURL({ sort: field, order: newOrder, page: 1 });
}

function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}


document.getElementById('searchInput').value = currentSearch;
document.getElementById('searchInput').addEventListener('input', debounce(function(e) {
    updateURL({ search: e.target.value, page: 1 });
}, 500));


function exportToExcel() {
    let eventid=<?php echo $eventId; ?>;
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = `user-export.php?event_id=${eventid}&${params.toString()}`;
}

document.querySelectorAll('[data-sort]').forEach(el => {
    el.addEventListener('click', (e) => {
        e.preventDefault();
        handleSort(el.dataset.sort);
    });
    
    if (el.dataset.sort === currentSort) {
        const icon = el.querySelector('i');
        icon.classList.remove('bi-arrow-down-up');
        icon.classList.add(currentOrder === 'ASC' ? 'bi-arrow-up' : 'bi-arrow-down');
    }
});
</script>

<?php include 'inc/footer.php'; ?>
