-- Settings
local HoldClick = true
local Hotkey = "t"
local HotkeyToggle = true

local Players = game:GetService("Players")
local RunService = game:GetService("RunService")
local UserInputService = game:GetService("UserInputService")

local LocalPlayer = Players.LocalPlayer
local Mouse = LocalPlayer:GetMouse()

local Enabled = false
local RightClickHeld = false
local CurrentlyPressed = false

-- Function to check if a target is on your team
local function isTeammate(targetPlayer)
	if targetPlayer and targetPlayer.Team == LocalPlayer.Team then
		return true
	end
	return false
end

-- Function to get player from any part of their character
local function getPlayerFromPart(part)
	if part and part.Parent then
		local character = part.Parent
		-- Check if parent has Humanoid (it's a character)
		if character:FindFirstChild("Humanoid") then
			return Players:GetPlayerFromCharacter(character)
		end
	end
	return nil
end

Mouse.KeyDown:Connect(function(key)
	key = key:lower()

	if key == Hotkey:lower() then
		if HotkeyToggle then
			Enabled = not Enabled
			print("Autotrigger:", Enabled and "ON" or "OFF")
		end
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
	if input.UserInputType == Enum.UserInputType.MouseButton2 then
		RightClickHeld = true
	end
end)

UserInputService.InputEnded:Connect(function(input, gameProcessed)
	if input.UserInputType == Enum.UserInputType.MouseButton2 then
		RightClickHeld = false

		if HoldClick and CurrentlyPressed then
			CurrentlyPressed = false
			mouse1release()
		end
	end
end)

RunService.RenderStepped:Connect(function()
	if Enabled and RightClickHeld then
		local targetPart = Mouse.Target
		if targetPart and targetPart.Parent:FindFirstChild("Humanoid") then
			local targetPlayer = getPlayerFromPart(targetPart)
			
			-- Skip if it's a teammate OR a player (only attack non-players like dummies)
			if targetPlayer and isTeammate(targetPlayer) then
				if HoldClick and CurrentlyPressed then
					CurrentlyPressed = false
					mouse1release()
				end
				return -- Skip teammates
			end
			
			-- Attack anyway (works on dummies AND enemies)
			if HoldClick then
				if not CurrentlyPressed then
					CurrentlyPressed = true
					mouse1press()
				end
			else
				mouse1click()
			end
		else
			if HoldClick and CurrentlyPressed then
				CurrentlyPressed = false
				mouse1release()
			end
		end
	else
		if HoldClick and CurrentlyPressed then
			CurrentlyPressed = false
			mouse1release()
		end
	end
end)
