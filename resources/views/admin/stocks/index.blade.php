@extends('admin.master')
@section('content')
<div class="content-wrapper">
   <div class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-sm-6">
               <h1 class="m-0">Stock Management</h1>
            </div>
            <div class="col-sm-6">
               <div class="pull-right">
                  <button class="btn btn-primary" data-toggle="modal" data-target="#addStockModal">
                     <i class="fas fa-plus"></i> Add Stock
                  </button>
                  <button class="btn btn-success" data-toggle="modal" data-target="#bulkAddModal">
                     <i class="fas fa-layer-group"></i> Bulk Add
                  </button>
               </div>
            </div>
         </div>
      </div>
   </div>

   <section class="content">
      <div class="container-fluid">
         <!-- Date Filter -->
         <div class="row mb-3">
            <div class="col-md-4">
               <label>Select Date:</label>
               <input type="date" id="filterDate" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
               <label>&nbsp;</label><br>
               <button class="btn btn-info" onclick="loadStockList()">
                  <i class="fas fa-search"></i> Load Stock
               </button>
               <button class="btn btn-warning" onclick="loadDailyReport()">
                  <i class="fas fa-chart-line"></i> Daily Report
               </button>
            </div>
            <div class="col-md-4">
               <label>Report Type:</label>
               <select id="reportType" class="form-control" onchange="loadReport()">
                  <option value="">Select Report</option>
                  <option value="datewise">Date-wise Report</option>
                  <option value="daily">Daily Report</option>
                  <option value="weekly">Weekly Report</option>
                  <option value="monthly">Monthly Report</option>
               </select>
            </div>
         </div>

         <!-- Alert Messages -->
         <div id="alertMessage" style="display:none;"></div>

         <!-- Stock List Table -->
         <div class="card">
            <div class="card-header">
               <h3 class="card-title">Stock List - <span id="currentDate">{{ date('Y-m-d') }}</span></h3>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped" id="stockTable">
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Brand</th>
                           <th>Size</th>
                           <th>Color</th>
                           <th>Quantity</th>
                           <th>Date</th>
                           <th>Actions</th>
                        </tr>
                     </thead>
                     <tbody id="stockTableBody">
                        <tr>
                           <td colspan="7" class="text-center">Loading...</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>

         <!-- Report Section -->
         <div class="card" id="reportCard" style="display:none;">
            <div class="card-header">
               <h3 class="card-title">Report</h3>
            </div>
            <div class="card-body">
               <div id="reportContent"></div>
            </div>
         </div>
      </div>
   </section>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Add Stock</h5>
            <button type="button" class="close" data-dismiss="modal">
               <span>&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="addStockForm">
               <div class="form-group">
                  <label>Brand *</label>
                  <input type="text" class="form-control" name="brand" required>
               </div>
               <div class="form-group">
                  <label>Size *</label>
                  <input type="text" class="form-control" name="size" placeholder="e.g., 256gb" required>
               </div>
               <div class="form-group">
                  <label>Color *</label>
                  <input type="text" class="form-control" name="color" required>
               </div>
               <div class="form-group">
                  <label>Quantity *</label>
                  <input type="number" class="form-control" name="quantity" min="0" required>
               </div>
               <div class="form-group">
                  <label>Date *</label>
                  <input type="date" class="form-control" name="stock_date" value="{{ date('Y-m-d') }}" required>
               </div>
               <div class="form-group">
                  <label>Notes</label>
                  <textarea class="form-control" name="notes" rows="2"></textarea>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="addStock()">Add Stock</button>
         </div>
      </div>
   </div>
</div>

<!-- Bulk Add Modal -->
<div class="modal fade" id="bulkAddModal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Bulk Add Stock</h5>
            <button type="button" class="close" data-dismiss="modal">
               <span>&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="form-group">
               <label>Date *</label>
               <input type="date" class="form-control" id="bulkDate" value="{{ date('Y-m-d') }}" required>
            </div>
            <div id="bulkStockItems">
               <div class="row mb-2">
                  <div class="col-md-3"><strong>Brand</strong></div>
                  <div class="col-md-2"><strong>Size</strong></div>
                  <div class="col-md-2"><strong>Color</strong></div>
                  <div class="col-md-2"><strong>Quantity</strong></div>
                  <div class="col-md-2"><strong>Action</strong></div>
               </div>
               <div class="stock-item-row mb-2">
                  <div class="row">
                     <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" name="bulk_brand[]" placeholder="Brand">
                     </div>
                     <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" name="bulk_size[]" placeholder="256gb">
                     </div>
                     <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" name="bulk_color[]" placeholder="Color">
                     </div>
                     <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm" name="bulk_quantity[]" placeholder="0" min="0">
                     </div>
                     <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeStockRow(this)">
                           <i class="fas fa-times"></i>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <button type="button" class="btn btn-sm btn-success" onclick="addStockRow()">
               <i class="fas fa-plus"></i> Add Row
            </button>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="bulkAddStock()">Add All</button>
         </div>
      </div>
   </div>
</div>

<!-- Edit Stock Modal -->
<div class="modal fade" id="editStockModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Edit Stock</h5>
            <button type="button" class="close" data-dismiss="modal">
               <span>&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="editStockForm">
               <input type="hidden" id="editStockId">
               <div class="form-group">
                  <label>Brand *</label>
                  <input type="text" class="form-control" id="editBrand" required>
               </div>
               <div class="form-group">
                  <label>Size *</label>
                  <input type="text" class="form-control" id="editSize" required>
               </div>
               <div class="form-group">
                  <label>Color *</label>
                  <input type="text" class="form-control" id="editColor" required>
               </div>
               <div class="form-group">
                  <label>Quantity *</label>
                  <input type="number" class="form-control" id="editQuantity" min="0" required>
               </div>
               <div class="form-group">
                  <label>Date *</label>
                  <input type="date" class="form-control" id="editStockDate" required>
               </div>
               <div class="form-group">
                  <label>Notes</label>
                  <textarea class="form-control" id="editNotes" rows="2"></textarea>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="updateStock()">Update Stock</button>
         </div>
      </div>
   </div>
</div>

<script>
const API_BASE = '{{ url("/api") }}';

// Load stock list
function loadStockList() {
   const date = document.getElementById('filterDate').value;
   document.getElementById('currentDate').textContent = date;
   document.getElementById('reportCard').style.display = 'none';
   
   fetch(`${API_BASE}/stock-list?date=${date}`)
      .then(response => response.json())
      .then(data => {
         const tbody = document.getElementById('stockTableBody');
         if (data.data && data.data.length > 0) {
            tbody.innerHTML = data.data.map(stock => `
               <tr>
                  <td>${stock.id}</td>
                  <td>${stock.brand}</td>
                  <td>${stock.size}</td>
                  <td>${stock.color}</td>
                  <td><strong>${stock.quantity}</strong></td>
                  <td>${stock.stock_date}</td>
                  <td>
                     <button class="btn btn-sm btn-info" onclick="editStock(${stock.id}, '${stock.brand}', '${stock.size}', '${stock.color}', ${stock.quantity}, '${stock.stock_date}', '${stock.notes || ''}')">
                        <i class="fas fa-edit"></i>
                     </button>
                     <button class="btn btn-sm btn-danger" onclick="deleteStock(${stock.id})">
                        <i class="fas fa-trash"></i>
                     </button>
                  </td>
               </tr>
            `).join('');
         } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">No stock found for this date</td></tr>';
         }
      })
      .catch(error => {
         showAlert('Error loading stock: ' + error.message, 'danger');
      });
}

// Add single stock
function addStock() {
   const form = document.getElementById('addStockForm');
   const formData = new FormData(form);
   const data = Object.fromEntries(formData);
   
   fetch(`${API_BASE}/stock-add`, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
   })
   .then(response => response.json())
   .then(data => {
      if (data.message) {
         showAlert(data.message, 'success');
         $('#addStockModal').modal('hide');
         form.reset();
         loadStockList();
      } else {
         showAlert(data.error || 'Error adding stock', 'danger');
      }
   })
   .catch(error => {
      showAlert('Error: ' + error.message, 'danger');
   });
}

// Bulk add stock
function bulkAddStock() {
   const date = document.getElementById('bulkDate').value;
   const brands = document.getElementsByName('bulk_brand[]');
   const sizes = document.getElementsByName('bulk_size[]');
   const colors = document.getElementsByName('bulk_color[]');
   const quantities = document.getElementsByName('bulk_quantity[]');
   
   const stocks = [];
   for (let i = 0; i < brands.length; i++) {
      if (brands[i].value && sizes[i].value && colors[i].value && quantities[i].value) {
         stocks.push({
            brand: brands[i].value,
            size: sizes[i].value,
            color: colors[i].value,
            quantity: parseInt(quantities[i].value)
         });
      }
   }
   
   if (stocks.length === 0) {
      showAlert('Please add at least one stock item', 'warning');
      return;
   }
   
   fetch(`${API_BASE}/stock-bulk-add`, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
      },
      body: JSON.stringify({
         stock_date: date,
         stocks: stocks
      })
   })
   .then(response => response.json())
   .then(data => {
      if (data.message) {
         showAlert(data.message, 'success');
         $('#bulkAddModal').modal('hide');
         document.getElementById('bulkStockItems').innerHTML = `
            <div class="row mb-2">
               <div class="col-md-3"><strong>Brand</strong></div>
               <div class="col-md-2"><strong>Size</strong></div>
               <div class="col-md-2"><strong>Color</strong></div>
               <div class="col-md-2"><strong>Quantity</strong></div>
               <div class="col-md-2"><strong>Action</strong></div>
            </div>
            <div class="stock-item-row mb-2">
               <div class="row">
                  <div class="col-md-3">
                     <input type="text" class="form-control form-control-sm" name="bulk_brand[]" placeholder="Brand">
                  </div>
                  <div class="col-md-2">
                     <input type="text" class="form-control form-control-sm" name="bulk_size[]" placeholder="256gb">
                  </div>
                  <div class="col-md-2">
                     <input type="text" class="form-control form-control-sm" name="bulk_color[]" placeholder="Color">
                  </div>
                  <div class="col-md-2">
                     <input type="number" class="form-control form-control-sm" name="bulk_quantity[]" placeholder="0" min="0">
                  </div>
                  <div class="col-md-2">
                     <button type="button" class="btn btn-sm btn-danger" onclick="removeStockRow(this)">
                        <i class="fas fa-times"></i>
                     </button>
                  </div>
               </div>
            </div>
         `;
         loadStockList();
      } else {
         showAlert(data.error || 'Error adding stocks', 'danger');
      }
   })
   .catch(error => {
      showAlert('Error: ' + error.message, 'danger');
   });
}

// Add stock row in bulk modal
function addStockRow() {
   const container = document.getElementById('bulkStockItems');
   const newRow = document.createElement('div');
   newRow.className = 'stock-item-row mb-2';
   newRow.innerHTML = `
      <div class="row">
         <div class="col-md-3">
            <input type="text" class="form-control form-control-sm" name="bulk_brand[]" placeholder="Brand">
         </div>
         <div class="col-md-2">
            <input type="text" class="form-control form-control-sm" name="bulk_size[]" placeholder="256gb">
         </div>
         <div class="col-md-2">
            <input type="text" class="form-control form-control-sm" name="bulk_color[]" placeholder="Color">
         </div>
         <div class="col-md-2">
            <input type="number" class="form-control form-control-sm" name="bulk_quantity[]" placeholder="0" min="0">
         </div>
         <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeStockRow(this)">
               <i class="fas fa-times"></i>
            </button>
         </div>
      </div>
   `;
   container.appendChild(newRow);
}

// Remove stock row
function removeStockRow(btn) {
   const rows = document.getElementsByClassName('stock-item-row');
   if (rows.length > 1) {
      btn.closest('.stock-item-row').remove();
   } else {
      showAlert('At least one row is required', 'warning');
   }
}

// Edit stock
function editStock(id, brand, size, color, quantity, date, notes) {
   document.getElementById('editStockId').value = id;
   document.getElementById('editBrand').value = brand;
   document.getElementById('editSize').value = size;
   document.getElementById('editColor').value = color;
   document.getElementById('editQuantity').value = quantity;
   document.getElementById('editStockDate').value = date;
   document.getElementById('editNotes').value = notes || '';
   $('#editStockModal').modal('show');
}

// Update stock - creates new entry with date-wise data
function updateStock() {
   const id = document.getElementById('editStockId').value;
   const quantity = parseInt(document.getElementById('editQuantity').value);
   const stockDate = document.getElementById('editStockDate').value;
   const notes = document.getElementById('editNotes').value;
   
   const data = {
      quantity: quantity,
      stock_date: stockDate,
      notes: notes
   };
   
   fetch(`${API_BASE}/stock-update/${id}`, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
   })
   .then(response => response.json())
   .then(data => {
      if (data.message) {
         showAlert(data.message, 'success');
         $('#editStockModal').modal('hide');
         loadStockList();
      } else {
         showAlert(data.error || 'Error updating stock', 'danger');
      }
   })
   .catch(error => {
      showAlert('Error: ' + error.message, 'danger');
   });
}

// Delete stock
function deleteStock(id) {
   if (confirm('Are you sure you want to delete this stock?')) {
      fetch(`${API_BASE}/stock-delete/${id}`)
         .then(response => response.json())
         .then(data => {
            if (data.message) {
               showAlert(data.message, 'success');
               loadStockList();
            } else {
               showAlert(data.error || 'Error deleting stock', 'danger');
            }
         })
         .catch(error => {
            showAlert('Error: ' + error.message, 'danger');
         });
   }
}

// Load daily report
function loadDailyReport() {
   const date = document.getElementById('filterDate').value;
   document.getElementById('reportCard').style.display = 'block';
   
   fetch(`${API_BASE}/stock-daily-report?date=${date}`)
      .then(response => response.json())
      .then(data => {
         if (data.data && data.data.length > 0) {
            let html = `<h5>Daily Report for ${date}</h5>`;
            html += `<p><strong>Previous Date:</strong> ${data.previous_date}</p>`;
            html += `<table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th>Brand</th>
                     <th>Size</th>
                     <th>Color</th>
                     <th>Previous Qty</th>
                     <th>Current Qty</th>
                     <th>Change</th>
                  </tr>
               </thead>
               <tbody>`;
            
            data.data.forEach(item => {
               const changeClass = item.change_type === 'plus' ? 'text-success' : 
                                  item.change_type === 'minus' ? 'text-danger' : 'text-muted';
               html += `
                  <tr>
                     <td>${item.brand}</td>
                     <td>${item.size}</td>
                     <td>${item.color}</td>
                     <td>${item.previous_quantity}</td>
                     <td><strong>${item.quantity}</strong></td>
                     <td class="${changeClass}"><strong>${item.change_text}</strong></td>
                  </tr>
               `;
            });
            
            html += `</tbody></table>`;
            document.getElementById('reportContent').innerHTML = html;
         } else {
            document.getElementById('reportContent').innerHTML = '<p>No data available for this date</p>';
         }
      })
      .catch(error => {
         showAlert('Error loading report: ' + error.message, 'danger');
      });
}

// Load date-wise report
function loadDateWiseReport() {
   const date = document.getElementById('filterDate').value;
   document.getElementById('reportCard').style.display = 'block';
   
   fetch(`${API_BASE}/stock-date-report?date=${date}`)
      .then(response => response.json())
      .then(data => {
         if (data.data && data.data.length > 0) {
            let html = `<h5>Date-wise Report for ${date}</h5>`;
            html += `<table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th>Brand</th>
                     <th>Size</th>
                     <th>Color</th>
                     <th>Add New</th>
                     <th>Minus</th>
                     <th>Remaining</th>
                     <th>Previous Qty</th>
                     <th>Notes</th>
                  </tr>
               </thead>
               <tbody>`;
            
            data.data.forEach(item => {
               html += `
                  <tr>
                     <td>${item.brand}</td>
                     <td>${item.size || '-'}</td>
                     <td>${item.color || '-'}</td>
                     <td class="text-success"><strong>${item.add_new}</strong></td>
                     <td class="text-danger"><strong>${item.minus}</strong></td>
                     <td class="text-primary"><strong>${item.remaining}</strong></td>
                     <td>${item.previous_quantity}</td>
                     <td>${item.notes || '-'}</td>
                  </tr>
               `;
            });
            
            html += `</tbody></table>`;
            document.getElementById('reportContent').innerHTML = html;
         } else {
            document.getElementById('reportContent').innerHTML = '<p>No data available for this date</p>';
         }
      })
      .catch(error => {
         showAlert('Error loading report: ' + error.message, 'danger');
      });
}

// Load report based on type
function loadReport() {
   const type = document.getElementById('reportType').value;
   if (!type) return;
   
   document.getElementById('reportCard').style.display = 'block';
   const date = document.getElementById('filterDate').value;
   
   if (type === 'datewise') {
      loadDateWiseReport();
      return;
   }
   
   let url = '';
   if (type === 'daily') {
      url = `${API_BASE}/stock-daily-report?date=${date}`;
   } else if (type === 'weekly') {
      const weekStart = new Date(date);
      weekStart.setDate(weekStart.getDate() - weekStart.getDay() + 1);
      url = `${API_BASE}/stock-weekly-report?week_start=${weekStart.toISOString().split('T')[0]}`;
   } else if (type === 'monthly') {
      const month = date.substring(0, 7);
      url = `${API_BASE}/stock-monthly-report?month=${month}`;
   }
   
   fetch(url)
      .then(response => response.json())
      .then(data => {
         let html = `<h5>${type.charAt(0).toUpperCase() + type.slice(1)} Report</h5>`;
         
         if (type === 'daily') {
            html += renderDailyReport(data);
         } else if (type === 'weekly') {
            html += renderWeeklyReport(data);
         } else if (type === 'monthly') {
            html += renderMonthlyReport(data);
         }
         
         document.getElementById('reportContent').innerHTML = html;
      })
      .catch(error => {
         showAlert('Error loading report: ' + error.message, 'danger');
      });
}

function renderDailyReport(data) {
   if (!data.data || data.data.length === 0) return '<p>No data available</p>';
   
   let html = `<p><strong>Date:</strong> ${data.date} | <strong>Previous:</strong> ${data.previous_date}</p>`;
   html += `<table class="table table-bordered table-striped">
      <thead><tr><th>Brand</th><th>Size</th><th>Color</th><th>Previous</th><th>Current</th><th>Change</th></tr></thead>
      <tbody>`;
   
   data.data.forEach(item => {
      const changeClass = item.change_type === 'plus' ? 'text-success' : 
                         item.change_type === 'minus' ? 'text-danger' : 'text-muted';
      html += `<tr>
         <td>${item.brand}</td>
         <td>${item.size}</td>
         <td>${item.color}</td>
         <td>${item.previous_quantity}</td>
         <td><strong>${item.quantity}</strong></td>
         <td class="${changeClass}"><strong>${item.change_text}</strong></td>
      </tr>`;
   });
   
   html += `</tbody></table>`;
   return html;
}

function renderWeeklyReport(data) {
   if (!data.data) return '<p>No data available</p>';
   
   let html = `<p><strong>Week:</strong> ${data.week_start} to ${data.week_end}</p>`;
   Object.keys(data.data).forEach(date => {
      html += `<h6>${date}</h6>`;
      if (data.data[date].length > 0) {
         html += `<table class="table table-sm table-bordered mb-3">
            <thead><tr><th>Brand</th><th>Size</th><th>Color</th><th>Qty</th><th>Change</th></tr></thead>
            <tbody>`;
         data.data[date].forEach(item => {
            const changeClass = item.change_type === 'plus' ? 'text-success' : 
                               item.change_type === 'minus' ? 'text-danger' : 'text-muted';
            html += `<tr>
               <td>${item.brand}</td>
               <td>${item.size}</td>
               <td>${item.color}</td>
               <td>${item.quantity}</td>
               <td class="${changeClass}">${item.change_text}</td>
            </tr>`;
         });
         html += `</tbody></table>`;
      }
   });
   return html;
}

function renderMonthlyReport(data) {
   if (!data.data) return '<p>No data available</p>';
   
   let html = `<p><strong>Month:</strong> ${data.month} (${data.month_start} to ${data.month_end})</p>`;
   Object.keys(data.data).forEach(date => {
      if (data.data[date].length > 0) {
         html += `<h6>${date}</h6>`;
         html += `<table class="table table-sm table-bordered mb-3">
            <thead><tr><th>Brand</th><th>Size</th><th>Color</th><th>Qty</th><th>Change</th></tr></thead>
            <tbody>`;
         data.data[date].forEach(item => {
            const changeClass = item.change_type === 'plus' ? 'text-success' : 
                               item.change_type === 'minus' ? 'text-danger' : 'text-muted';
            html += `<tr>
               <td>${item.brand}</td>
               <td>${item.size}</td>
               <td>${item.color}</td>
               <td>${item.quantity}</td>
               <td class="${changeClass}">${item.change_text}</td>
            </tr>`;
         });
         html += `</tbody></table>`;
      }
   });
   return html;
}

// Show alert message
function showAlert(message, type) {
   const alertDiv = document.getElementById('alertMessage');
   alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
   alertDiv.innerHTML = `
      ${message}
      <button type="button" class="close" onclick="this.parentElement.style.display='none'">
         <span>&times;</span>
      </button>
   `;
   alertDiv.style.display = 'block';
   setTimeout(() => {
      alertDiv.style.display = 'none';
   }, 5000);
}

// Load stock list on page load
document.addEventListener('DOMContentLoaded', function() {
   loadStockList();
});
</script>
@endsection

