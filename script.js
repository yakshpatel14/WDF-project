document.addEventListener("DOMContentLoaded", () => {
const faqData = [
    { question: "IS DELL THE BEST SELLING COMPANY", answer: "Since 2022 DELL became the india's best laptop selling company with much benifits " },
    { question: "payment method?", answer: "for online payment you have provided the best option that is the debit card with much offers" },
    { question:"how to contact us?", answer:"you can contact us via email and the phone number provided in the contact us section" },
    {question:"how to track my order?", answer:"you will be able to track your order deeply after 15 days due to maintainance of the website and we are building a new sftweare for tracking your order."}
];
const faqContainer = document.getElementById("faq-container");
faqData.forEach((item) => {
const faqItem = document.createElement("div");
faqItem.classList.add("faq-item");
const question = document.createElement("div");
question.classList.add("faq-question");
question.textContent = item.question;
const answer = document.createElement("div");
answer.classList.add("faq-answer");
answer.textContent = item.answer;
question.addEventListener("click", () => {
answer.style.display = answer.style.display === "block" ? "none" : "block";
});
faqItem.appendChild(question);
faqItem.appendChild(answer);
faqContainer.appendChild(faqItem);
});
document.getElementById("closePopup").addEventListener("click", () => {
document.getElementById("notification").style.display = "none";
});
});