// Caminho do arquivo Word a ser carregado automaticamente
const filePath = "./relatorio.docx";

// Função para carregar e exibir o conteúdo do Word
function loadDocx(filePath) {
    fetch(filePath)
        .then(response => response.arrayBuffer())
        .then(arrayBuffer => {
            // Usando o mammoth.js para converter o arquivo DOCX para HTML
            mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
                .then(result => {
                    // Obtendo o HTML convertido
                    const htmlContent = result.value;

                    // Inserindo o HTML convertido na página
                    document.getElementById("relatorio").innerHTML = htmlContent;

                    // Garantindo que o conteúdo seja não-editável
                    document.getElementById("relatorio").contentEditable = "false";
                })
                .catch(err => {
                    console.error("Erro ao processar o arquivo Word:", err);
                    document.getElementById("relatorio").innerHTML = 
                        "<p>Erro ao carregar o relatório. Tente novamente mais tarde.</p>";
                });
        })
        .catch(err => {
            console.error("Erro ao carregar o arquivo:", err);
            document.getElementById("relatorio").innerHTML = 
                "<p>Erro ao acessar o arquivo do relatório. Verifique se ele está disponível.</p>";
        });
}

// Carregar o arquivo automaticamente ao iniciar a página
window.onload = () => {
    loadDocx(filePath);
};
