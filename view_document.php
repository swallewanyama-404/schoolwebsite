<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . BASE_URL . 'admissions.php');
    exit();
}

$stmt = $pdo->prepare('SELECT * FROM admissions_documents WHERE id = ? AND is_active = 1');
$stmt->execute([$id]);
$doc = $stmt->fetch();

if (!$doc) {
    header('Location: ' . BASE_URL . 'admissions.php');
    exit();
}

require_once __DIR__ . '/includes/header.php';
?>

<main style="padding: 2rem 1rem; background: #f4f6f9; min-height: 85vh; font-family: sans-serif;">
    <div style="max-width: 1400px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">
        
        <div style="background: #e3f2fd; color: #0d47a1; border-left: 4px solid #1976d2; padding: 1rem; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            📄 You are previewing the official contents of <strong><?php echo htmlspecialchars($doc['title']); ?></strong>. Use the control panel actions to save a hardcopy to your device storage.
        </div>

        <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem; align-items: start;">
            
            <div style="background: #fff; border: 1px solid #e2e8f0; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: sticky; top: 20px;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem; text-align: center;">📋</div>
                <h3 style="margin: 0 0 0.5rem 0; color: #1a202c; text-align: center; font-size: 1.35rem;">
                    <?php echo htmlspecialchars($doc['title']); ?>
                </h3>
                <p style="color: #718096; text-align: center; font-size: 0.85rem; margin-bottom: 2rem; border-bottom: 1px solid #edf2f7; padding-bottom: 1rem;">
                    Type: Adobe PDF Document Layout
                </p>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="<?php echo BASE_URL; ?>process_download.php?id=<?php echo $id; ?>" 
                       style="display: block; text-align: center; padding: 0.8rem 1.5rem; background: #0B3C5D; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 0.95rem; box-shadow: 0 2px 4px rgba(11,60,93,0.2);">
                        📥 Download PDF File
                    </a>
                    
                    <a href="<?php echo BASE_URL; ?>admissions.php" 
                       style="display: block; text-align: center; padding: 0.8rem 1.5rem; background: #e2e8f0; color: #4a5568; text-decoration: none; border-radius: 6px; font-size: 0.95rem; font-weight: 500;">
                        ← Back to Admissions
                    </a>
                </div>
            </div>

            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; height: 750px;">
                <object data="<?php echo BASE_URL; ?>process_download.php?id=<?php echo $id; ?>&stream=1" type="application/pdf" style="width: 100%; height: 100%;">
                    <div style="padding: 3rem; text-align: center; color: #4a5568;">
                        <p style="font-size: 1.1rem; margin-bottom: 1rem;">Your web browser does not support previewing PDFs inline directly.</p>
                        <a href="<?php echo BASE_URL; ?>process_download.php?id=<?php echo $id; ?>&stream=1" target="_blank" style="color: #0b3c5d; font-weight: bold; text-decoration: underline;">
                            Click here to open the content stream in a new browser tab instead.
                        </a>
                    </div>
                </object>
            </div>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>