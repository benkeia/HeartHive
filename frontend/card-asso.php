<style>
    html {
        font-family: 'Montserrat', sans-serif;
    }

    div {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    img {
        width: 100%;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
    }

    .loc {
        font-size: 0.8rem;
        margin: 0;
    }


    .tag {
        background-color: #f0f0f0;
        padding: 5px;
        border-radius: 20px;
        margin: 10px 0;
        padding: 5px 10px;
    }

    .desc {
        font-size: 0.8rem;
        margin: 0;
        text-align: left;
        text-overflow: ellipsis;
        overflow: hidden;
        overflow-wrap: break-word;
        width: inherit;
        height: max-content;


        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 6;
        /* Limite à 3 lignes */
        overflow: hidden;


    }

    .card {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        width: 300px;
        height: 400px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;


    }

    .subcard {
        margin: 15px 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        height: auto;
    }
</style>
<div>

    <div class="card">
        <img src="assets/uploads/background/example.jpg" alt="">

        <div class="subcard">
            <h2 class="card-title">Professeur</h2>
            <p class="loc">à 4.5km</p>
        </div>
        <p class="tag">📖 Tutorat</p>
        <p class="desc">Do officia ipsum dolor anim excepteur. Cupidatat nulla non ullamco in. In officia eiusmod reprehenderit minim irure culpa amet fugiat veniam nisi quis laborum. Aliqua ex commodo est incididunt amet voluptate. Ut dolor adipisicing fugiat pariatur qui pariatur aute irure dolore voluptate laboris cillum officia in sint.</p>
    </div>
</div>