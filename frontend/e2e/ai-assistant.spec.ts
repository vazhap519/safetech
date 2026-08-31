import { expect, test } from "@playwright/test";

test("AI assistant renders and enforces privacy consent", async ({ page }) => {
    await page.goto("/");

    const openButton = page.getByRole("button", {
        name: /AI კონსულტანტის გახსნა|Open AI consultant|Открыть AI-консультанта/i,
    });
    await expect(openButton).toBeVisible();
    await openButton.click();

    const assistant = page.getByRole("dialog", {
        name: /SafeTech AI კონსულტანტი|SafeTech AI Consultant|AI-консультант SafeTech/i,
    });
    await expect(assistant).toBeVisible();

    const textarea = assistant.locator("textarea");
    await textarea.fill("QA privacy consent probe");
    await assistant
        .getByRole("button", { name: /გაგზავნა|Send|Отправить/i })
        .click();

    await expect(
        assistant.getByText(
            /მონიშნეთ მონაცემების დამუშავებაზე თანხმობა|accept data processing|подтвердите согласие на обработку данных/i,
        ),
    ).toBeVisible();
});
