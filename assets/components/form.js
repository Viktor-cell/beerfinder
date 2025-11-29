let form = {
    
    template:`
    <form action="/index.php/?lang=<?= $lang ?>" method="POST">
                
        <label for="select-beer"><?= $t["formBeer"]["selectBeer"]?> </label>
        <input type="text" id="select-beer" name="select-beer" value="<?= $_POST['select-beer']?>"><br>
        
        
        <?php if(isset($_POST['select-beer'])): ?>
            <label for="select-beer"><?= $t["what"]["city"]?></label>
            <input type="text" id="city" name="city" value="<?= $_POST['city']?>"><br>
        <?php endif?>
        <input type="submit" value="<?= $t["header"]["langMenu"]["button"]?>">

    </form>

    <button>
        <p><?= $t["noBeer"]["idk"]?></p>
    </button>
    `
}


export default form