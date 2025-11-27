<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f5f5f5; display: flex; }
    .sidebar { width: 250px; background: #1f2937; color: white; min-height: 100vh; padding: 20px; position: fixed; }
    .sidebar h2 { margin-bottom: 30px; font-size: 24px; }
    .sidebar h2 span { color: #3b82f6; }
    .sidebar ul { list-style: none; }
    .sidebar li { margin-bottom: 10px; }
    .sidebar a { color: #d1d5db; text-decoration: none; display: block; padding: 10px; border-radius: 8px; transition: all 0.2s; }
    .sidebar a:hover, .sidebar a.active { background: rgba(59,130,246,0.2); color: #3b82f6; }
    .logout-btn { margin-top: 30px; padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-size: 14px; font-weight: 600; }
    .logout-btn:hover { background: #b91c1c; }
    .main-content { margin-left: 250px; flex: 1; padding: 30px; }
    .container { max-width: 1200px; }
    h1 { margin-bottom: 10px; font-size: 32px; color: #333; }
    p { color: #666; margin-bottom: 30px; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .stat-icon { font-size: 32px; margin-bottom: 10px; }
    .stat-number { font-size: 36px; font-weight: 700; color: #333; margin-bottom: 5px; }
    .stat-label { color: #666; font-size: 14px; margin-bottom: 10px; }
    .stat-link { color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 600; }
    .quick-actions { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .quick-actions h2 { margin-bottom: 20px; font-size: 20px; }
    .action-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
    .btn { padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; border: none; cursor: pointer; }
    .btn-primary { background: #3b82f6; color: white; }
    .btn-primary:hover { background: #2563eb; }
    .btn-secondary { background: #f3f4f6; color: #333; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-danger { background: #dc2626; color: white; }
    .btn-danger:hover { background: #b91c1c; }
    table { width: 100%; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    th { background: #f9fafb; padding: 15px; text-align: left; font-weight: 700; color: #666; font-size: 12px; text-transform: uppercase; }
    td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
    tr:last-child td { border-bottom: none; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
    .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
    .form-group textarea { min-height: 100px; resize: vertical; }
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
    .modal.active { display: flex; align-items: center; justify-content: center; }
    .modal-content { background: white; padding: 30px; border-radius: 16px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
</style>