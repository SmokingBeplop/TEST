local Players = game:GetService("Players")
local UserInputService = game:GetService("UserInputService")
local RunService = game:GetService("RunService")

local LocalPlayer = Players.LocalPlayer
local Mouse = LocalPlayer:GetMouse()

local HoldClick = true
local Hotkey = "t"
local HotkeyToggle = true

local Enabled = false
local RightClickHeld = false
local CurrentlyActive = false

local function getCharacterFromTarget(target)
	if not target then
		return nil
	end
	return target:FindFirstAncestorOfClass("Model")
end

local function isEnemyCharacter(character)
	if not character then
		return false
	end

	local humanoid = character:FindFirstChildOfClass("Humanoid")
	if not humanoid then
		return false
	end

	local targetPlayer = Players:GetPlayerFromCharacter(character)
	if not targetPlayer then
		return false
	end

	if targetPlayer == LocalPlayer then
		return false
	end

	if LocalPlayer.Team ~= nil and targetPlayer.Team ~= nil then
		return LocalPlayer.Team ~= targetPlayer.Team
	end

	return true
end

Mouse.KeyDown:Connect(function(key)
	key = key:lower()

	if key == Hotkey:lower() then
		if HotkeyToggle then
			Enabled = not Enabled
			print("Target system:", Enabled and "ON" or "OFF")
		else
			Enabled = true
		end
	end
end)

Mouse.KeyUp:Connect(function(key)
	key = key:lower()

	if not HotkeyToggle and key == Hotkey:lower() then
		Enabled = false
	end
end)

UserInputService.InputBegan:Connect(function(input, gameProcessed)
	if gameProcessed then
		return
	end

	if input.UserInputType == Enum.UserInputType.MouseButton2 then
		RightClickHeld = true
	end
end)

UserInputService.InputEnded:Connect(function(input)
	if input.UserInputType == Enum.UserInputType.MouseButton2 then
		RightClickHeld = false
		CurrentlyActive = false
	end
end)

RunService.RenderStepped:Connect(function()
	if not Enabled or not RightClickHeld then
		CurrentlyActive = false
		return
	end

	local targetPart = Mouse.Target
	local character = getCharacterFromTarget(targetPart)

	if character and isEnemyCharacter(character) then
		if HoldClick then
			if not CurrentlyActive then
				CurrentlyActive = true
				print("Valid enemy target")
			end
		else
			print("Clicked enemy target")
		end
	else
		CurrentlyActive = false
	end
end)
