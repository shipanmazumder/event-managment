<?php 
include 'inc/header.php';
include 'inc/sidebar.php';
include 'class/event.php';

try {
    $eventObj = new event();
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

$events = $eventObj->getPaginatedEvents($page, $limit, $search, $sortField, $sortOrder);
?>
<?php
    if(isset($_GET['deleteEvent'])){
        $deleteEventId=preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['deleteEvent']);
        $eventDelete=$eventObj->delete($deleteEventId);
        header("Location:index.php?search=$search&page=$page&limit=$limit&sort=$sortField&order=sortOrder");
    }
?>
<?php
try {
    if(isset($_GET['toggleEvent'])){
        $toggleEventId=preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['toggleEvent']);
        $eventToggle=$eventObj->toggle($toggleEventId);
        header("Location:index.php?search=$search&page=$page&limit=$limit&sort=$sortField&order=sortOrder");
    }
} catch (Exception $e) {
    echo "Error: " . $e;
    exit();
}
?>

    <div class="container mt-4">
        <h2>My Events</h2>
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search events...">
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        <button class="btn btn-success" onclick="exportToExcel()">
                            <i class="bi bi-file-earmark-excel"></i> Export to CSV
                        </button>
                    </div>
                </div>
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-striped table-hover " style="min-width: 1500px;">
                        <thead>
                            <tr>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="name">Event Name <i class="bi bi-arrow-down-up"></i></a></th>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="event_date">Date <i class="bi bi-arrow-down-up"></i></a></th>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="event_time">Time <i class="bi bi-arrow-down-up"></i></a></th>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="location">Location <i class="bi bi-arrow-down-up"></i></a></th>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="description">Description <i class="bi bi-arrow-down-up"></i></a></th>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="maximum_participant">Maximum Paricipant <i class="bi bi-arrow-down-up"></i></a></th>
                                <th><a href="#" class="text-decoration-none text-dark" data-sort="total_participate">Total Register <i class="bi bi-arrow-down-up"></i></a></th>
                                <th><a href="#" class="text-decoration-none text-dark">Attendees </a></th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="eventsTableBody">
                            <?php if (!empty($events['data'])): ?>
                                <?php foreach ($events['data'] as $event): ?>
                                    <tr>
                                        <td ><?php echo $event['name']; ?></td>
                                        <td ><?php echo date('d M Y', strtotime($event['event_date'])); ?></td>
                                        <td ><?php echo date('h:i A', strtotime($event['event_time'])); ?></td>
                                        <td ><?php echo $event['location']; ?></td>
                                        <td ><?php echo htmlspecialchars($fm->textshorten($event['description'],30)); ?></td>
                                        <td ><?php echo htmlspecialchars($event['maximum_participant']); ?></td>
                                        <td><?php echo htmlspecialchars($event['total_participate']); ?></td>
                                        <td >
                                            <a href="event-users.php?event_id=<?php echo $event['id']; ?>" class="btn btn-info btn-sm text-white">
                                                <i class="bi bi-people"></i> Attendees
                                            </a>
                                        </td>
                                        <td >
                                            <!-- <a href="#" data-id="<?php echo $event['id']; ?>" class="btn btn-warning btn-sm text-white attendee-modal-btn">
                                                <i class="bi bi-eye"></i> View
                                            </a> -->
                                            <button type="button" class="btn btn-warning btn-sm text-white view-event" data-id="<?php echo $event['id']; ?>">
                                                <i class="bi bi-eye"></i> View
                                            </button>

                                        <a target="_blank" href="edit-event.php?id=<?php echo $event['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="?deleteEvent=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm ms-1" onclick="return confirm('Are you sure you want to delete this event?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                        <a href="?toggleEvent=<?php echo $event['id']; ?>" class="btn btn-<?php echo $event['status'] ? 'success' : 'secondary'; ?> btn-sm ms-1">
                                            <i class="bi bi-toggle-<?php echo $event['status'] ? 'on' : 'off'; ?>"></i> <?php echo $event['status'] ? 'Active' : 'Inactive'; ?>
                                        </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No events found</td>
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
                                Showing <?php echo ($page - 1) * $limit + 1; ?>-<?php echo min($page * $limit, $events['total']); ?> 
                                of <?php echo $events['total']; ?> events
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="Page navigation" class="float-md-end">
                            <ul class="pagination" id="pagination">
                                <?php if ($events['totalPages'] > 1): ?>
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($page - 1); ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortField; ?>&order=<?php echo $sortOrder; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $events['totalPages']; $i++): ?>
                                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortField; ?>&order=<?php echo $sortOrder; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $events['totalPages']): ?>
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


<!-- Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('.view-event').click(function() {
        // $('.modal-body').html('Loading...');
        //  $('#eventModal').modal('show');
        let eventId = $(this).data('id');
        $.ajax({
            url: 'get-event-details.php',
            type: 'GET',
            data: { id: eventId },
            dataType: 'json',
            success: function(event) {
                let result=event.data
                $('#eventModalLabel').html(result.name);
                let modalBody = `
                    <p><strong>Date:</strong> ${result.event_date}</p>
                    <p><strong>Time:</strong> ${result.event_time}</p>
                    <p><strong>Location:</strong> ${result.location}</p>
                    <p><strong>Description:</strong><br /> ${result.description}</p>
                    <p><strong>Maximum Participants:</strong> ${result.maximum_participant}</p>
                    <p><strong>Total Registered:</strong> ${result.total_participate}</p>
                `;
                $('.modal-body').html(modalBody);
                $('#eventModal').modal('show');
            }
        });
    });
});
</script>

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

// Export to Excel function
function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = `export.php?${params.toString()}`;
}

// Update sort icons
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
