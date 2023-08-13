<div class="custome-breadcrumb has-actions">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= baseUrl("dashboard") ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?= baseUrl("users") ?>">کاربران</a></li>
            <li class="breadcrumb-item active">لیست</li>
        </ol>
    </nav>
    <div class="actions">
        <button class="btn btn-primary btn-ajax-views btn-float-icon" data-url="users/add">
            <?= inlineIcon("add", "icon") ?>
            افزودن
        </button>
        <button class="btn btn-danger btn-float-icon" style="display: none;" id="btn-bulk-delete">
            <?= inlineIcon("trash", "icon") ?>
            حذف گروهی
        </button>
    </div>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table id="users-table" class="table" style="width: 100%;">
            <tbody>

            </tbody>
        </table>
    </div>
</div>


<div class="modal" tabindex="-1" id="online-users-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">کاربران آنلاین</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
          
            </div>
        </div>
    </div>
</div>